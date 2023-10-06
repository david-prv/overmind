import shutil, json, os, sys, time

def folder_to_archive(dir: str, out_name: str = None) -> None:
    """Deflates a folder to a zip-archive

    Parameters
    ----------
    dir: str
        The source directory which will be compressed
    our_name: str
        The output archive name (without extension)
    """

    shutil.make_archive(base_name=dir if out_name == None else out_name, format="zip", root_dir=dir)

def parse_mapper(used_cwd: str, mapper_file: str = "/app/tools/map.json") -> list:
    """Parses the map.json file

    Parameters
    ----------
    used_cwd: str
        The current root directory of the framework
    mapper_file: str
        The expected location of the mapper file

    Returns
    ----------
    list
        A list of json interpreted data of tools
    """

    real_mf = os.path.abspath(used_cwd + mapper_file)

    if not os.path.isfile(real_mf):
        raise Exception(f"Could not find mapper file: {real_mf}")

    f_handle = open(real_mf, "r")

    return json.loads(f_handle.read())

def parse_schedules_refs(used_cwd: str, tool_id: str, schedule_file: str = "/app/tools/interactions.json") -> tuple:
    """Parses the interactions.json and reads the ref file

    Parameters
    ----------
    used_cwd: str
        The current root directory of the framework
    tool_id: str
        The currently visited tool's id
    schedule_file: str
        The expected location of the schedule file

    Returns
    ----------
    tuple
        A tuple containing the schedule and the reference
    """

    # (1) parse schedule
    real_sf = os.path.abspath(used_cwd + schedule_file)

    if not os.path.isfile(real_sf):
        raise Exception(f"Could not find schedule: {real_sf}")

    f_handle = open(real_sf, "r")
    all_schedules = json.loads(f_handle.read())

    schedule = []
    if tool_id in all_schedules:
        schedule = all_schedules[tool_id]

    f_handle.close()
    
    # (2) parse reference
    ref_path = os.path.abspath(used_cwd + f"/refs/ref_{tool_id}.txt")

    if not os.path.isfile(ref_path):
        raise Exception(f"Could not find reference: {ref_path}")

    f_handle = open(ref_path, "r")
    f_content = f_handle.read()
    f_handle.close()

    if not "|" in f_content:
        raise Exception(f"Reference '{tool_id}' doesn't contain a hash-sum")

    reference = f_content.split("|")[0]

    return (schedule, reference)

def ask_info() -> tuple:
    """Reads user information which will be used as snapshot details

    Returns
    ----------
    tuple
        A tuple containing the author and the description
    """

    _author = input("> snapshot author = ")
    _description = input("> snapshot description = ")

    return (_author, _description)

def prepare_snapshot(used_cwd: str, snap_info: tuple) -> None:
    """Prepares a temporary folder to create the snapshot

    Parameters
    ----------
    used_cwd: str
        The current root directory of the framework
    snap_info: tuple
        The provided snapshot details
    """

    target_base_dir = os.path.abspath(used_cwd + "/snapshot")
    if os.path.isdir(target_base_dir):
        raise Exception(f"Folder {target_base_dir} already exists!")

    # create folder structure
    os.mkdir(target_base_dir)
    os.mkdir(target_base_dir + "/_extra")
    os.mkdir(target_base_dir + "/_tools")

    # create snapshot info files
    f_handle_author = open(target_base_dir + "/.author", "w")
    f_handle_info = open(target_base_dir + "/.info", "w")
    
    # write info
    f_handle_author.write(snap_info[0])
    f_handle_info.write(snap_info[1])

    # close file handlers
    f_handle_author.close()
    f_handle_info.close()

def create_tool_folder(used_cwd: str, tool_name: str) -> None:
    """Creates a sub-folder in the _tools section of the snapshot

    Parameters
    ----------
    used_cwd: str
        The current root directory of the framework
    tool_name: str
        The currently visited tool's name
    """

    tool_namespace = tool_name.lower()
    target_tool_dir = os.path.abspath(used_cwd + f"/snapshot/_tools/{tool_namespace}")

    if os.path.isdir(target_tool_dir):
        raise Exception(f"Folder {target_tool_dir} already exists!")

    os.mkdir(target_tool_dir)

def create_tool_infos(used_cwd: str, tool_name: str, tool_info: str, tool_schedule: str, tool_reference: str) -> None:
    """Parses the interactions.json and reads the ref file

    Parameters
    ----------
    used_cwd: str
        The current root directory of the framework
    tool_name: str
        The currently visited tool's name
    tool_info: str
        The provided tool information for .info file
    tool_schedule: str
        The provided tool information for .schedule file
    tool_reference: str
        The provided tool information for .reference file
    """

    tool_namespace = tool_name.lower()
    target_tool_dir = os.path.abspath(used_cwd + f"/snapshot/_tools/{tool_namespace}")
    if not os.path.isdir(target_tool_dir):
        raise Exception(f"Tool target dir is missing: {target_tool_dir}")

    # write .info file
    f_handle_info = open(target_tool_dir + f"/{tool_namespace}.info", "w")
    f_handle_info.write(tool_info)
    f_handle_info.close()

    # write .schedule file
    f_handle_schedule = open(target_tool_dir + f"/{tool_namespace}.schedule", "w")
    f_handle_schedule.write(tool_schedule)
    f_handle_schedule.close()

    # write .reference file
    f_handle_reference = open(target_tool_dir + f"/{tool_namespace}.reference", "w")
    f_handle_reference.write(tool_reference)
    f_handle_reference.close()

def create_tool_zip(used_cwd: str, tool_name: str, tool_src: str = None, debug: bool = True) -> None:
    """Deflates a tool's files and puts it into the snapshot

    Parameters
    ----------
    used_cwd: str
        The current root directory of the framework
    tool_id: str
        The currently visited tool's name
    tool_src: str
        Overrides the locally used namespace
    """

    # "namespace" = the used name for snapshot, which is
    # always the lowercase name
    tool_namespace = tool_name.lower()
    tool_src = tool_src if tool_src != None else tool_name
    source_tool_dir = os.path.abspath(used_cwd + f"/app/tools/{tool_src}")
    if debug: print(f"    -> Took it from {source_tool_dir}")
    target_tool_dir = os.path.abspath(used_cwd + f"/snapshot/_tools/{tool_namespace}")

    # now we create the zip archive
    target_zip = target_tool_dir + f"/{tool_namespace}"
    folder_to_archive(dir=source_tool_dir, out_name=target_zip)

def clean_up_temp_folder(used_cwd: str) -> None:
    """Cleans up the created temporary folder

    Parameters
    ----------
    used_cwd: str
        The current root directory of the framework
    """
    temp_folder = os.path.abspath(used_cwd + "/snapshot")
    if not os.path.isdir(temp_folder):
        raise Exception("Could not clean-up temp folder!")

    shutil.rmtree(temp_folder)

def main() -> None:
    """The main section of the script"""

    # defining log output and cwd
    root_dir = sys.argv[1] if len(sys.argv) > 1 else os.getcwd()
    sys.stdout = open(f"{root_dir}/logs/snapshot-creator.log", "a")

    print(f"[*] Snapshot Creator started at {int(time.time())}!")
    print(f"[*] Reading data...")

    # reading map data
    map_data = parse_mapper(used_cwd=root_dir)
    print(f"[*] Found {len(map_data)} tools!")

    # check for passed data
    passed_author = sys.argv[2] if len(sys.argv) >= 3 else None
    passed_description = sys.argv[3] if len(sys.argv) >= 4 else None
    did_pass_valid = passed_author != None and passed_description != None

    # asking for information
    if not did_pass_valid:
        print(f"[*] Please enter the snapshot details:")
        given_info = ask_info()
    else:
        given_info = [passed_author, passed_description]
    prepare_snapshot(used_cwd=root_dir, snap_info=given_info)

    for tool in map_data:
        # tool id for reference and schedule
        _id = tool["id"]

        # the schedule and reference
        _schedule, _reference = parse_schedules_refs(used_cwd=root_dir, tool_id=_id)

        # the following order is important!
        _name = tool["name"]
        _author = tool["author"]
        _url = tool["url"]
        _version = tool["version"]
        _engine = tool["engine"]
        _index = tool["index"]
        _args = tool["args"]
        _description = tool["description"]
        _keywords = tool["keywords"]

        # convert data to proper strings
        _str_reference = _reference.strip()
        _str_info = f"{_name}\n{_author}\n{_url}\n{_version}\n{_engine}\n{_index}\n{_args}\n{_description}\n{_keywords}".strip()
        _str_schedule = "\n".join(_schedule).strip()

        print(f"[*] Deflating {_name}...")

        # write tool data to snapshot folder
        _tmp = _index.split("/")
        _local_name = _tmp[len(_tmp)-2]
        create_tool_folder(used_cwd=root_dir, tool_name=_name)
        create_tool_infos(used_cwd=root_dir, tool_name=_name, tool_info=_str_info, tool_schedule=_str_schedule, tool_reference=_str_reference)
        create_tool_zip(used_cwd=root_dir, tool_name=_name, tool_src=_name if _name == _local_name else _local_name)

    # deflating whole temp folder
    folder_to_archive(dir=root_dir + "/snapshot", out_name=root_dir + "/snapshot-" + str(int(time.time())))

    # clean everything up
    print("[*] Cleaning up...")
    clean_up_temp_folder(used_cwd=root_dir)

    print("[*] Done!")
    exit()

if __name__ == "__main__":
    main()