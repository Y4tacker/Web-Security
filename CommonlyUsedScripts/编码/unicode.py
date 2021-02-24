import sys
print(''.join([hex(ord(c)).replace('0x', '\\\\u00') for c in sys.argv[1]]))
