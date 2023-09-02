import shutil, json, os, sys

def folder_to_archive(dir: str, out_name: str = None) -> None:
    shutil.make_archive(base_name=dir if out_name == None else out_name, format="zip", root_dir=dir)

def parse_mapper(used_cwd: str, mapper_file: str = "/app/tools/map.json") -> list:
    real_mf = os.path.abspath(used_cwd + mapper_file)
    # TODO: Add file checks here
    f_handle = open(real_mf, "r")
    return json.loads(f_handle.read())

def parse_schedules_refs(used_cwd: str, tool_id: str, schedule_file: str = "/app/tools/interactions.json") -> list:
    # (1) parse schedule
    real_sf = os.path.abspath(used_cwd + schedule_file)
    # TODO: Add file checks here too
    f_handle = open(real_sf, "r")
    all_schedules = json.loads(f_handle.read())
    schedule = []
    if tool_id in all_schedules:
        schedule = all_schedules[tool_id]
    f_handle.close()
    
    # (2) parse reference
    ref_path = os.path.abspath(used_cwd + f"/refs/ref_{tool_id}.txt")
    if not os.path.isfile(ref_path): raise Exception(f"Could not find reference: {ref_path}")
    f_handle = open(ref_path, "r")
    f_content = f_handle.read()
    f_handle.close()
    if not "|" in f_content: raise Exception(f"Reference '{tool_id}' doesn't contain a hashsum")
    reference = f_content.split("|")[0]

    return [schedule, reference]

def ask_info() -> list:
    _author = input("> snapshot author = ")
    _description = input("> snapshot description = ")
    return [_author, _description]

def prepare_snapshot(snap_info: list) -> None:
    target_base_dir = os.path.abspath(os.getcwd() + "/snapshot")
    if os.path.isdir(target_base_dir): raise Exception(f"Folder {target_base_dir} already exists!")

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
    tool_namespace = tool_name.lower()
    target_tool_dir = os.path.abspath(used_cwd + f"/snapshot/_tools/{tool_namespace}")
    if os.path.isdir(target_tool_dir): raise Exception(f"Folder {target_tool_dir} already exists!")
    os.mkdir(target_tool_dir)

def create_tool_infos(used_cwd: str, tool_name: str, tool_info: str, tool_schedule: str, tool_reference: str) -> None:
    tool_namespace = tool_name.lower()
    target_tool_dir = os.path.abspath(used_cwd + f"/snapshot/_tools/{tool_namespace}")
    # TODO: You know what belongs here, right?

    f_handle_info = open(target_tool_dir + f"/{tool_namespace}.info", "w")
    f_handle_info.write(tool_info)
    f_handle_info.close()

    f_handle_schedule = open(target_tool_dir + f"/{tool_namespace}.schedule", "w")
    f_handle_schedule.write(tool_schedule)
    f_handle_schedule.close()

    f_handle_reference = open(target_tool_dir + f"/{tool_namespace}.reference", "w")
    f_handle_reference.write(tool_reference)
    f_handle_reference.close()

def create_tool_zip(used_cwd: str, tool_name: str) -> None:
    tool_namespace = tool_name.lower()
    source_tool_dir = os.path.abspath(used_cwd + f"/app/tools/{tool_name}")
    target_tool_dir = os.path.abspath(used_cwd + f"/snapshot/_tools/{tool_namespace}")
    target_zip = target_tool_dir + f"/{tool_namespace}"
    folder_to_archive(dir=source_tool_dir, out_name=target_zip)

def clean_up_temp_folder(used_cwd: str) -> None:
    temp_folder = os.path.abspath(used_cwd + "/snapshot")
    if not os.path.isdir(temp_folder): raise Exception("Could not clean-up temp folder!")
    shutil.rmtree(temp_folder)

def main() -> None:
    print(f"[*] Snapshot Creator started!")
    print(f"[*] Reading data...")

    # reading map data and cwd
    root_dir = sys.argv[1] if len(sys.argv) > 1 else os.getcwd()
    map_data = parse_mapper(used_cwd=root_dir)
    print(f"[*] Found {len(map_data)} tools!")

    # asking for information
    print(f"[*] Please enter the snapshot details:")
    given_info = ask_info()
    prepare_snapshot(snap_info=given_info)

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
        create_tool_folder(used_cwd=root_dir, tool_name=_name)
        create_tool_infos(used_cwd=root_dir, tool_name=_name, tool_info=_str_info, tool_schedule=_str_schedule, tool_reference=_str_reference)
        create_tool_zip(used_cwd=root_dir, tool_name=_name)

    # deflating whole temp folder
    folder_to_archive(dir=root_dir + "/snapshot")

    # clean everything up
    print("[*] Cleaning up...")
    clean_up_temp_folder(used_cwd=root_dir)

    print("[*] Done!")
    exit()

if __name__ == "__main__":
    main()