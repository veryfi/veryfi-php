def main():

    import xml.etree.ElementTree as ET
    import os

    tree = ET.parse("../report.xml")
    root = tree.getroot()
    metrics = root[0][-1].attrib
    code_coverage = int(metrics['coveredstatements']) / int(metrics['statements'])
    verify_unit_tests(code_coverage)
    methods_covered = int(metrics['coveredmethods']) / int(metrics['methods'])
    code_coverage, code_coverage_color = number_to_badge(code_coverage)
    methods_coverage, methods_covered_color = number_to_badge(methods_covered)
    os.system("wget --output-document=code_coverage.svg https://img.shields.io/badge/code%20coverage-{}-{}".format
              (code_coverage, code_coverage_color))
    os.system("wget --output-document=methods_coverage.svg https://img.shields.io/badge/methods%20coverage-{}-{}".format
              (methods_coverage, methods_covered_color))


def number_to_badge(number):

    color = get_color(number)
    number *= 100
    if number % 10 == 0:
        return str(number) + "%25", color
    return str(round(number, 1)) + "%25", color


def get_color(number):

    if number < 0.6:
        return "red"
    if number < 0.8:
        return "yellow"
    if number < 0.92:
        return "green"
    return "brightgreen"


def verify_unit_tests(number):

    if number < 0.92:
        print("Code coverage < 92%")
        exit(-1)


if __name__ == "__main__":

    main()
