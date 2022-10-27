from subprocess import Popen, PIPE, STDOUT
import os, sys, json

"""
This script is an alternative runner script
for tools/scanners which use interactive shell inputs.
The interactive runner reads the interactions.json file and
decides whether there are interactions or not. If so,
it loads the pre-defined answers and communicates them to the running tool.
"""

def main() -> None:
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
    target = sys.argv[5]

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
    used_data = str.encode(tmp)

    print(args, used_data)

    out = open("./reports/report_" + str(id) + ".txt", "w", encoding="utf-8")
    p = Popen(args, stdout=out, stdin=PIPE, stderr=out)
    p.communicate(input=used_data)[0]

    out.close()
    exit()

if __name__ == "__main__":
    main()