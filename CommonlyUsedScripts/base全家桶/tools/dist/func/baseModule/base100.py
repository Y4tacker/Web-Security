def encode(data, encoding="utf-8"):
    """
    Encodes text to emoji
    :param data: Original text as bytes array or plaintext
    :param encoding: (Optional) encoding if {data} passed as plaintext
    :return: bytes array of encoded text
    """
    if isinstance(data, str):
        data = data.encode(encoding)
    out = [240, 159, 0, 0]*len(data)
    for i, b in enumerate(data):
        out[4*i+2] = (b + 55) // 64 + 143
        out[4*i+3] = (b + 55) % 64 + 128
    return bytes(out)


def decode(data, encoding="utf-8"):
    """
    Decodes emoji to text
    :param data: Encoded text in form of emoji as bytes array or plaintext
    :param encoding: (Optional) encoding if {data} passed as plaintext
    :return: bytes array of decoded text
    """
    if isinstance(data, str):
        data = data.encode(encoding)
    if len(data) % 4 != 0:
        raise Exception('Length of string should be divisible by 4')
    tmp = 0
    out = [None]*(len(data) // 4)
    for i, b in enumerate(data):
        if i % 4 == 2:
            tmp = ((b - 143) * 64) % 256
        elif i % 4 == 3:
            out[i//4] = (b - 128 + tmp - 55) & 0xff
    return bytes(out)