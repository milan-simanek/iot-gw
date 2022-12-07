# IoT message gateway

This software exposes IoT HUB on REST interface and allows to receive
messages using HTTP POST or pulls messages from a REST message queue using
HTTP GET. It can also generate a message upon request HTTP GET.

The software is modular, each module implements separamte message group.

This is the core module + getmsg module to fetch messages from message
queue.

Cons:
  - no authentication
