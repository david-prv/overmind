from subprocess import Popen, PIPE, STDOUT
from threading import Timer
import os, sys, json, re

"""
This script is an alternative runner script
for tools/scanners which use interactive shell inputs.
The interactive runner reads the interactions.json file and
decides whether there are interactions or not. If so,
it loads the pre-defined answers and communicates them to the running tool.
"""

EXEC_TIMEOUT = 20

def get_exec_timeout() -> int:
    """Reads the execution timeout

    Returns
    ----------
    int
        The execution timeout
    """

    try:
        return int(open('./app/tools/timeout').readline())
    except:
        return EXEC_TIMEOUT

def remove_ansi_from_file(file_location: str) -> None:
    """Removes any ANSI characters from a file

    Parameters
    ----------
    file_location: str
        The file that should get cleaned up
    """

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
        f = open(file_location, 'r+', encoding="utf-8")
        content = f.read()
        f.truncate(0)
        f.seek(0)
        f.write(ansi_escape.sub('', content))

        f.close()
    except:
        print("ERROR: File was not found or permission mismatch")
        return

def main() -> None:
    """The main section of the script"""

    try:
        j = open('./app/tools/interactions.json')
    except FileNotFoundError:
        print("ERROR: File was not found")
        return

    data = json.load(j)

    engine = sys.argv[1]
    app = sys.argv[2]
    cmd = sys.argv[3]
    id = sys.argv[4]
    target = clean_target = sys.argv[5]

    if "://" in target:
        clean_target = target.split("://")[1]

    if not id in data:
        print("ERROR: No interactive data found")
        return

    if len(data[id]) <= 0:
        print("ERROR: Interactive data set is empty")
        return

    args = [engine, app]
    args.extend(cmd.split(" "))

    tmp = "\n".join(data[id]) + "\n"
    tmp = tmp.replace("%URL%", target)
    tmp = tmp.replace("%RAW%", clean_target)
    used_data = str.encode(tmp)

    print(args, used_data)

    out = open("./reports/report_" + str(id) + ".txt", "w", encoding="utf-8")

    p = Popen(args, stdout=out, stdin=PIPE, stderr=out)
    timer = Timer(get_exec_timeout(), p.kill)

    try:
        print("Starting timer...")
        timer.start()
        print("Initializing communication...")
        p.communicate(input=used_data)[0]
        timer.cancel()
    except:
        timer.cancel()

    # close fp
    out.close()

    # clean up output
    remove_ansi_from_file("./reports/report_" + str(id) + ".txt")

    # exit application
    exit()

if __name__ == "__main__":
    main()