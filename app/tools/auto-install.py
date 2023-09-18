import sys, os

REQUIREMENTS = {
    "python": "requirements.txt",
    "node": ["package.json", "package-lock.json"]
}

SHELL = {
    "python": "pip install -r {}",
    "node": "npm install"
}

def get_req_files(engine: str) -> str|list:
    return REQUIREMENTS[engine.lower()] if engine.lower() in REQUIREMENTS else ""

def get_req_shell(engine: str) -> str|list:
    return SHELL[engine.lower()] if engine.lower() in SHELL else ""

def esc_argument(argument):
    return '"%s"' % (
        argument
        .replace('\\', '\\\\')
        .replace('"', '\\"')
        .replace('$', '\\$')
        .replace('`', '\\`')
    )

def has_requirements(path: str, engine: str) -> bool:
    if not os.path.isdir(path): return False

    req = get_req_files(engine)
    if isinstance(req, list):
        for _req in req:
            if not os.path.isfile(f"{path}/{_req}"):
                return False
        return True
    else:
        return os.path.isfile(f"{path}/{req}")

def install_requirements(path: str, engine: str) -> bool:
    if not (os.path.isdir(path) and os.path.isabs(path)):
        print(f"[!] Tool information could not be accessed!")
        return False

    req = get_req_files(engine) if not isinstance(get_req_files(engine), list) else ""
    shell = get_req_shell(engine)

    if isinstance(shell, list):
        exitCode = 0
        for _shell in shell:
            exitCode += os.system(f"cd {path} && {_shell.format(esc_argument(req))}")
        return exitCode == 0
    else:
        _shell = f"cd {path} && {shell.format(esc_argument(req))}"
        return os.system(_shell) == 0

def main(cwd: str = "./scanner-bundle") -> None:
    # tool information: "<toolName>|<toolEngine>[|<altToolNamespace>]"
    # python3 setup.py "nodeTool|node" "pythonTool|python" "pythonNested|python|pythonNested/pythonNested"
    toolList = sys.argv[1:] if len(sys.argv) > 1 else None

    if toolList == None:
        print("[*] No tools to setup. Quitting...")
        exit()

    print(f"[*] Recognized {len(toolList)} tools to setup!")

    for tool in toolList:
        if not "|" in tool:
            print(f"[!] Invalid argument '{tool}'!")
            continue

        _toolData = tool.split("|")
        _toolName = _toolData[0]
        _toolEngine = _toolData[1]
        _toolPath = os.path.abspath(f"{cwd}/app/tools/{_toolName}") if len(_toolData) <= 2 else os.path.abspath(f"{cwd}/app/tools/{_toolData[2]}")

        if not os.path.isdir(_toolPath):
            print(f"[!] Tool '{_toolPath}' not found!")
            continue

        if not has_requirements(_toolPath, _toolEngine):
            print(f"[!] Tool '{_toolName}' does not provide any requirement information.")
            continue

        print(f"[*] Tool '{_toolName}' has passed all checks")

        if not install_requirements(_toolPath, _toolEngine):
            print(f"[!] Installation for '{_toolName}' has failed!")
            continue

        print(f"[*] Successfully installed dependencies for {_toolName}!")

if __name__ == "__main__":
    main()