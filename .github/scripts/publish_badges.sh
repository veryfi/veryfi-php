#!/usr/bin/env bash
# Publishes the generated coverage badges to a dedicated branch.
#
# The default branch is protected and only accepts changes through pull
# requests, so CI cannot commit the regenerated badges back to it.
set -euo pipefail

cd "$(git rev-parse --show-toplevel)"

BRANCH="${BADGES_BRANCH:-badges}"
AUTHOR_NAME="${BADGES_AUTHOR_NAME:-Github actions}"
AUTHOR_EMAIL="${BADGES_AUTHOR_EMAIL:-veryfi@veryfi.com}"
COMMIT_MESSAGE="${BADGES_COMMIT_MESSAGE:-Update report}"
BADGES=(metrics/code_coverage.svg metrics/methods_coverage.svg)

for badge in "${BADGES[@]}"; do
    if [ ! -f "$badge" ]; then
        echo "Badge not found: $badge" >&2
        exit 1
    fi
done

worktree="$(mktemp -d)/badges"
temp_branch="publish-badges-$$"

cleanup() {
    git worktree remove --force "$worktree" >/dev/null 2>&1 || true
    git branch -D "$temp_branch" >/dev/null 2>&1 || true
    rm -rf "$(dirname "$worktree")"
}
trap cleanup EXIT

remote_ref="refs/remotes/origin/$BRANCH"
git fetch --no-tags --depth=1 --force origin \
    "+refs/heads/$BRANCH:$remote_ref" >/dev/null 2>&1 || true

if git rev-parse --verify --quiet "$remote_ref" >/dev/null 2>&1; then
    git worktree add --quiet -b "$temp_branch" "$worktree" "$remote_ref"
else
    git worktree add --quiet --detach "$worktree"
    git -C "$worktree" checkout --quiet --orphan "$temp_branch"
    git -C "$worktree" rm -rq --cached . >/dev/null 2>&1 || true
    find "$worktree" -mindepth 1 -maxdepth 1 ! -name .git -exec rm -rf {} +
fi

mkdir -p "$worktree/metrics"
cp "${BADGES[@]}" "$worktree/metrics/"
git -C "$worktree" add --force metrics

if git -C "$worktree" diff --cached --quiet; then
    echo "Coverage badges are unchanged, nothing to publish."
    exit 0
fi

git -C "$worktree" \
    -c "user.name=$AUTHOR_NAME" \
    -c "user.email=$AUTHOR_EMAIL" \
    commit --quiet --message "$COMMIT_MESSAGE"
git -C "$worktree" push --quiet origin "HEAD:refs/heads/$BRANCH"

echo "Published coverage badges to the '$BRANCH' branch."
