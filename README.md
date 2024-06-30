# skyrecorder

This is the continuation of what was put on ice in 2019. Everything was redone from scratch.

The main purpose is to record the sky and create monthly timelapses with as little work as possible. All\* I have to do is to copy the daily videos of the previous month, cut out the nights, speed up the scene, and upload it to the interwebs ;)

\* = still need to delete old/unused files from time to time *(but i'll automate that too probably)*

Checkout the [website](https://sky.etrusci.org) for the latest view and some more extra info.

I'll extend this documentation soon with more technical information.

<!--
## Hardware

recorder:

- Raspberry Pi 4 Model B 4GB (Rev 1.1)
- Logitech C920 HD Pro Webcam

cruncher:

- HP EliteDesk 705 G1 DM
- Western Digital Elements 1 TB External Hard Drive



## Setup

OS: Debian 12

Run setup/installdeps on both recorder and cruncher to install required software.

Run setup/sysupd on both recorder and cruncher from time to time.

Manually check for recorder/bin/mediamtx/mediamtx updates from time to time.

Setup passwordless keyauth ssh connection between both recorder and cruncher.

Add job to crontab on cruncher to run cruncher/bin/procrec yesterday every night at 0300 or so.
-->

<!-- 
## Network Setup

Nothing should be exposed to the internet. Only the **cruncher** needs outgoing internet access to upload the latest view to the web.

**cruncher**:
- hostname: elity
- ip: 192.168.13.111

**recorder**:
- hostname: studiopi
- ip: 192.168.13.117
-->
