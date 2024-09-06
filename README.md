# skyrecorder

This is the continuation of what was put on ice in 2019. Everything was redone from scratch.

The main purpose is to record the sky and create monthly timelapses with as little work as possible. All\* I have to do is to copy the daily videos of the previous month, cut out the nights, speed up the scene, and upload it to the interwebs ;)

\* = still need to delete old/unused files from time to time *(but i'll automate that too probably)*

Checkout the [website](https://sky.etrusci.org) for the timelapse video archive and the recent view.

This documentation is mainly just here so I don't forget. You may need to adjust the paths and hostnames in all scripts if you clone this repository to your system. There is currently no plan to make this more portable.




## Hardware

**recorder**:
- Raspberry Pi 4 Model B 4GB (Rev 1.1)
- Logitech C920 HD Pro Webcam

**cruncher**:
- HP EliteDesk 705 G1 DM
- Western Digital Elements 1 TB External Hard Drive




## Network Setup

Nothing should be exposed to the internet. Only the **cruncher** needs outgoing internet access to upload the latest view to the web. I still have to rename the hostnames... but for now those will do.

**cruncher**:
- hostname: elity

**recorder**:
- hostname: studiopi



## Filesystem Setup

**recorder**:

Root: `/home/art2/skyrecorder/`

**cruncher**:

Root: `/mnt/stor1/skyrecorder/`




## Recorder and Cruncher Setup

OS for both: Debian 12

Run [shared/bin/installdeps](./shared/bin/installdeps) on both **recorder** and **cruncher** to install required software.

Configure passwordless keyauth SSH connection between **recorder** and **cruncher**.

Add job to crontab on **cruncher** to run `cruncher/bin/bakedate yesterday` daily. See [cruncher/crontab.txt](./cruncher/crontab.txt).




## Start System

**startbgworkers** will create screen sessions. List them with `screen -ls` or resume with `screen -r <session_name_or_number>`.

**recorder**:

Run [recorder/bin/startbgworkers](./recorder/bin/startbgworkers).

**cruncher**:

Run [cruncher/bin/startbgworkers](./cruncher/bin/startbgworkers).




## Maintenance

Make sure to clean up old files on **cruncher** from time to time since this is not automated yet. On the **recorder** old files will be deleted after a pre-configured timeframe (see `recordDeleteAfter` in [recorder/bin/mediamtx/skyrecorder.yml](./recorder/bin/mediamtx/skyrecorder.yml)).

Run [shared/bin/sysupd](./shared/bin/sysupd) on both **recorder** and **cruncher** from time to time.

Manually check for [mediamtx](https://github.com/bluenviron/mediamtx/releases) updates from time to time.




## License

See [LICENSE](./LICENSE.md).
