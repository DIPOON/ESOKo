import binascii
import sys
import io


sys.stdout = io.TextIOWrapper(sys.stdout.detach(), encoding = 'utf-8')
sys.stderr = io.TextIOWrapper(sys.stderr.detach(), encoding = 'utf-8')


if __name__ == '__main__':
    str_val = '瀰'.encode('utf-8')
    hex_val = binascii.hexlify(str_val).decode('utf-8')
    checkwhere = 0

    print(bytes.fromhex(hex_val).decode('utf-8'))
    hex_val = int(hex_val, 16)
    print(format(hex_val, 'x'))

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

    print(hex_val)
    print()
    hex_val = format(hex_val, 'x')
    print(hex_val)
    print(bytes.fromhex(hex_val).decode('utf-8'))
    print(checkwhere)