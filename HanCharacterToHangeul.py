if __name__ == '__main__':
    str_val = '瀰'.encode('utf-8')
    print(str_val)
    print(str_val.decode('utf-8'))
    hex_val = int.from_bytes(str_val, byteorder='big')

    if hex_val >= 0xE18480 and hex_val <= 0xE187BF:
        hex_val = hex_val + 0x43400
        checkwhere = 1
    elif hex_val > 0xE384B0 and hex_val <= 0xE384BF:
        hex_val = hex_val + 0x237D0
        checkwhere = 2
    elif hex_val > 0xE38580 and hex_val <= 0xE3868F:
        hex_val = hex_val + 0x23710
        checkwhere = 3
    elif hex_val >= 0xEAB080 and hex_val <= 0xED9EAC:
        if hex_val >= 0xEAB880 and hex_val <= 0xEABFBF:
            hex_val = hex_val - 0x33800
            checkwhere = 4
        elif hex_val >= 0xEBB880 and hex_val <= 0xEBBFBF:
            hex_val = hex_val - 0x33800
            checkwhere = 5
        elif hex_val >= 0xECB880 and hex_val <= 0xECBFBF:
            hex_val = hex_val - 0x33800
            checkwhere = 6
        else:
            hex_val = hex_val - 0x3F800
            checkwhere = 7
    elif hex_val >= 0xE6B880 and hex_val <= 0xE9A6A3:
        hex_val = hex_val + 0x33800
        # 3F800이 오류나서 33800으로 바꿈
        checkwhere = 8

    print()
    print(hex_val)
    print()

    After_val = hex_val.to_bytes(3, byteorder='big')
    print(After_val)
    print(After_val.decode('utf-8'))