from pathlib import Path
from argparse import ArgumentParser
from urllib.parse import urlparse
from ui import *


def header(string: str):
    split = string.split(":")
    return {split[0].strip(): split[1].strip()}


def main():
    parser = ArgumentParser(description="Dump source code from LFI vulnerable website")
    parser.add_argument('-u', '--url', type=str, required=True,
                        help="Vulnerable url")
    parser.add_argument('-m', '--main', required=True, help="Path to main file")
    parser.add_argument('-H', '--headers', type=header, help="Additional headers")
    parser.add_argument('-d', '--data', help="Post request data")
    parser.add_argument("-e", "--encoding", choices=['base64'], help="Payload encoding")

    args = parser.parse_args()
    lfi_url = args.url
    main_file = args.main
    headers = args.headers
    data = args.data
    encoding = args.encoding

    dir_name = urlparse(lfi_url).netloc
    dump_dir = Path(Path.cwd() / dir_name)

    if dump_dir.exists():
        print(STAR + "Dump directory already exists")
    else:
        print(STAR + "Creating dump directory")
        dump_dir.mkdir()

    lang = Path(main_file).suffix
    match lang:
        case ".py":
            from py_dump import flask_dump

        case ".php":
            pass
        case ".js":
            pass
        case _:
            print(MINUS + "Unsupported language")


if __name__ == "__main__":
    main()
