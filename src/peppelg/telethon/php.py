import json
import sys
from telethon.sync import TelegramClient

def write(array):
    print(json.dumps(array, default=str))

def callback(event):
    write({'type': 'event', event: event})

while True:
    try:
        request = json.loads(input())
        if request['type'] == 'new TelegramClient':
            client = TelegramClient(request['name'], request['api_id'], request['api_hash'])
            write({'type': 'response', 'success': True})
        if request['type'] == 'exit':
            write({'type': 'response', 'success': True})
            quit()
        if request['type'] == 'TelegramClient':
            if type(request['args']) is dict:
                response = getattr(client, request['method'])(**request['args'])
            else:
                response = getattr(client, request['method'])(*request['args'])
            try:
                response = response.to_dict()
            except:
                pass
            write({'type': 'response', 'success': True, 'response': response})
        if request['type'] == 'new callback': #broken
            write({'type': 'event'})
            client.add_event_handler(callback)
            client.run_until_disconnected()
    except Exception as e:
        write({'type': 'error', 'success': False, 'error': str(e), 'exception': type(e).__name__})