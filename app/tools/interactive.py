from subprocess import Popen, PIPE, STDOUT
import os, sys, json

def main() -> None:
    try:
        j = open('interactions.json')
    except FileNotFoundError:
        print("ERROR: File was not found")
        return

    data = json.load(j)

    engine = sys.argv[1]
    app = sys.argv[2]
    cmd = sys.argv[3]
    id = sys.argv[4]

    if not id in data:
        print("ERROR: No interactive data found")
        return

    if len(data[id]) <= 0:
        print("ERROR: Interactive data set is empty")
        return

    args = [engine, app]
    args.extend(cmd.split(" "))

    used_data = str.encode("\n".join(data[id]) + "\n")

    print(args, used_data)

    out = open("./reports/report_" + str(id) + ".txt", "w", encoding="utf-8")
    p = Popen(args, stdout=out, stdin=PIPE, stderr=out)
    p.communicate(input=used_data)[0]

    out.close()
    exit()

if __name__ == "__main__":
    main()