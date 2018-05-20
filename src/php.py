from telethon import TelegramClient
#from telethon.tl import types, functions
import json
while True:
    try:
        php = input()
        if php:
            action = json.loads(php)
            try:
                if action['action'] == 'new_client':
                    client = TelegramClient(action['session'], action['api_id'], action['api_hash'])
                    client.connect()
                    print(json.dumps({'result': True}))
                if action['action'] == 'call':
                    print(json.dumps({'result': getattr(client, action['method'])(*action['args']).to_dict()}))
            except BaseException as e:
                print(json.dumps({'result': 'error', 'error': format(e)}))
    except EOFError:
        pass
