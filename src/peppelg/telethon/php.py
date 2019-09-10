import json
import sys
import threading
from telethon.sync import TelegramClient
import asyncio
updates = []
running = False


def write(array):
    print(json.dumps(array, default=str))


async def callback(event):
    global updates
    updates.append(event.to_dict())


def newCallback():
    client.add_event_handler(callback)
    client.run_until_disconnected()


def callClient(request):
    if running:  # broken
        loop = asyncio.get_event_loop()
        if type(request['args']) is dict:
            response = loop.run_until_complete(
                getattr(client, request['method'])(**request['args']))
        else:
            response = loop.run_until_complete(
                getattr(client, request['method'])(*request['args']))
        try:
            response = response.to_dict()
        except:
            pass
    else:
        if type(request['args']) is dict:
            response = getattr(client, request['method'])(**request['args'])
        else:
            response = getattr(client, request['method'])(*request['args'])
        try:
            response = response.to_dict()
        except:
            pass
    return response


while True:
    try:
        request = json.loads(input())
        if request['type'] == 'new TelegramClient':
            client = TelegramClient(
                request['name'], request['api_id'], request['api_hash'])
            write({'type': 'response', 'success': True})
        if request['type'] == 'exit':
            write({'type': 'response', 'success': True})
            quit()
        if request['type'] == 'TelegramClient':
            response = callClient(request)
            write({'type': 'response', 'success': True, 'response': response})
        if request['type'] == 'new callback':
            running = True
            threading.Thread(target=newCallback, daemon=True).start()
            write({'type': 'response', 'success': True})
        if request['type'] == 'getUpdate':
            while True:
                if updates:
                    write({'type': 'event', 'event': updates[0]})
                    del updates[0]
                    break

    except Exception as e:
        write({'type': 'error', 'success': False,
               'error': str(e), 'exception': type(e).__name__})
