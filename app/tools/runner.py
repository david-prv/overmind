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

EXEC_TIMEOUT = 20

def getExecTimeout() -> int:
    try:
        return int(open('./app/tools/timeout').readline())
    except:
        return EXEC_TIMEOUT

def removeANSIFromOutput(fileLocation) -> None:
    # Stolen from:
    # https://stackoverflow.com/questions/2424000/read-and-overwrite-a-file-in-python
    ansi_escape = re.compile(r'''
        \x1B  # ESC
        (?:   # 7-bit C1 Fe (except CSI)
            [@-Z\\-_]
        |     # or [ for CSI, followed by a control sequence
            \[
            [0-?]*  # Parameter bytes
            [ -/]*  # Intermediate bytes
            [@-~]   # Final byte
        )
        ''', re.VERBOSE)

    try:
        f = open(fileLocation, 'r+', encoding="utf-8")
        content = f.read()
        f.truncate(0)
        new_content = ansi_escape.sub('', content)
        f.seek(0)
        f.write(new_content)

        f.close()
    except:
        print("ERROR: File was not found or permission mismatch")
        return

def main() -> None:
    # Example:
    #                   Engine  App         Cmd                Id
    # python3 runner.py python3 Test/app.py https://etage-4.de 3
    engine = sys.argv[1]
    app = sys.argv[2]
    cmd = sys.argv[3]
    id = sys.argv[4]

    args = [engine, app]
    args.extend(cmd.split(" "))

    print(args)

    r = subprocess.run(args, stdout=subprocess.PIPE, timeout=getExecTimeout())
    try:
        r = r.stdout.decode('ascii')
    except UnicodeDecodeError:
        try:
            r = r.stdout.decode('utf-8')
        except UnicodeDecodeError:
            print("UnicodeDecodeError")

    f = open("./reports/report_" + str(id) + ".txt", "w", encoding="utf-8")
    f.write(r)

    # close fp
    f.close()

    # clean up output
    removeANSIFromOutput("./reports/report_" + str(id) + ".txt")

    # exit application
    exit()

if __name__ == '__main__':
    main()
