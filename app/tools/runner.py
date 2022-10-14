#!/usr/bin/env python3

import subprocess
import sys

"""
This runner allows us to run arbitrary
tools without actually running them via php.
This has the advantage that python can handle interactive
shell commands way easier and better than php.

DISCLAIMER: this should not be deployed to a live system!
This tool is meant to be used only LOCALLY!
"""

def main():
    engine = sys.argv[1]
    app = sys.argv[2]
    cmd = sys.argv[3]
    id = sys.argv[4]

    args = [engine, app]
    args.extend(cmd.split(" "))

    print(args)

    r = subprocess.run(args, stdout=subprocess.PIPE)
    try:
        r = r.stdout.decode('ascii')
    except UnicodeDecodeError:
        try:
            r = r.stdout.decode('utf-8')
        except UnicodeDecodeError:
            print("UnicodeDecodeError")

    f = open("./reports/report_" + str(id) + ".txt", "w", encoding="utf-8")
    f.write(r)
    f.close()

    exit()


if __name__ == '__main__':
    main()
