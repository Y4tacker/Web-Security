# -*- coding=utf-8 -*-
import argparse

parse = argparse.ArgumentParser()

parse.add_argument('-v', '--version', type=int, default=3)
parse.add_argument('-n', '--number', type=int)

args = parse.parse_args()
VERSION = args.version
NUMBER = args.number

# 还可以str()<str(None)姿势太多了 []<()等等
def getNumber2(number):
    number = int(number)
    if number in [-2, -1, 0, 1]:
        return ["~({}<[])", "~([]<[])",
                "([]<[])", "({}<[])"][number + 2]

    if number % 2:
        return "~%s" % getNumber2(~number)
    else:
        return "(%s<<({}<[]))" % getNumber2(number / 2)


def getNumber3(number):
    number = int(number)
    if number in [-2, -1, 0, 1]:
        return ["~int((len(str(None))/len(str(None))))", "~int(len(str()))",
                "int(len(str()))", "int((len(str(None))/len(str(None))))"][number + 2]

    if number % 2:
        return "~%s" % getNumber3(~number)
    else:
        return "(%s<<(int((len(str(None))/len(str(None))))))" % getNumber3(number / 2)


if __name__ == '__main__':
    if 2 == VERSION:
        print(getNumber2(NUMBER))
    else:
        print(getNumber3(NUMBER))
