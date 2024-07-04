if __name__ == '__main__':
    text_file_path = './kr.lang_42_10.csv'
    new_file_path = './kr_no_backslashquotation.csv'
    with open(text_file_path, 'r', encoding='UTF8') as f:
        with open(new_file_path, 'w', encoding='UTF8') as k:
            while True:
                line = f.readline()
                if not line:
                    break
                line = line.replace("\\\"", "\\ \"")
                print(line)
                k.write(line)
            k.close()
        f.close()