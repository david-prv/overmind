import sys, os, time

REQUIREMENTS = {
    "python": "requirements.txt",
    "node": ["package.json", "package-lock.json"]
}

SHELL = {
    "python": "pip install -r {}",
    "node": "npm install"
}

ALIAS = {
    "python": ["py3", "py2", "python3", "python2"],
    "node": ["nodejs", "js", "npm"]
}

def resolve_req_alias(engine: str) -> str:
    for x in ALIAS:
        if engine in ALIAS[x]:
            return REQUIREMENTS[x]
    return ""

def resolve_shell_alias(engine: str) -> str:
    for x in ALIAS:
        if engine in ALIAS[x]:
            return SHELL[x]
    return ""

def get_req_files(engine: str) -> str|list:
    return REQUIREMENTS[engine.lower()] if engine.lower() in REQUIREMENTS else resolve_req_alias(engine.lower())

def get_req_shell(engine: str) -> str|list:
    return SHELL[engine.lower()] if engine.lower() in SHELL else resolve_shell_alias(engine.lower())

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

def main(debug: bool = True) -> None:
    # tool information: "<toolName>|<toolEngine>[|<altToolNamespace>]"
    # run from \app\core\components: python ..\..\tools\auto-install.py "SSL-Verify|python"

    # runs from \app\core\components\Scanner.php
    root_dir = root_dir = sys.argv[1] if len(sys.argv) > 1 else os.getcwd()
    sys.stdout = open(os.path.abspath(f"{root_dir}/../../logs/auto-installer.log"), "a")

    toolList = sys.argv[2:] if len(sys.argv) > 2 else None

    print(f"[*] Auto-Installer started at {time.time()}!")

    if toolList == None:
        print("[*] No tools to setup. Quitting...")
        exit()

    print(f"[*] Recognized {len(toolList)} tools to setup!")
    print(f"[*] Using '{root_dir}' as current working directory!")

    for tool in toolList:
        if not "|" in tool:
            print(f"[!] Invalid argument '{tool}'!")
            continue

        _toolData = tool.split("|")
        _toolName = _toolData[0]
        _toolEngine = _toolData[1]
        _toolPath = os.path.abspath(f"{root_dir}/app/tools/{_toolName}") if len(_toolData) <= 2 else os.path.abspath(f"{root_dir}/app/tools/{_toolData[2]}")

        if debug: print(f"    -> Path={_toolPath}, Engine={_toolEngine}")

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

    print("[*] Done!")
    exit()

if __name__ == "__main__":
    main()