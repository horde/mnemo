Installing Mnemo H5
===================

This document contains instructions for installing the Mnemo web-based
notes application on your system.

For information on the capabilities and features of Mnemo, see the file
[README] in the top-level directory of the Mnemo distribution.

Prerequisites
-------------

To function properly, Mnemo **requires** the following:

1.  A working Horde installation.

    Mnemo runs within the [Horde Application Framework][README], a set
    of common tools for web applications written in PHP. You must
    install Horde before installing Mnemo.

    ::: {.important}
    ::: {.admonition-title}
    Important
    :::

    Mnemo H5 requires version 5.0+ of the Horde Framework -earlier
    versions of Horde will **not** work.
    :::

    ::: {.important}
    ::: {.admonition-title}
    Important
    :::

    Be sure to have completed all of the steps in the
    [horde/doc/INSTALL][README] file for the Horde Framework before
    installing Mnemo. Many of Mnemo\'s prerequisites are also Horde
    prerequisites. Additionally, many of Mnemo\'s optional features are
    configured via the Horde install.
    :::

2.  SQL support in PHP *or* a configured Kolab Server.

    Mnemo will store its data in either an SQL database or on a Kolab
    Server. If you use SQL, build PHP with whichever SQL driver you
    require; see the Horde [INSTALL][README] file for details.

Installing Mnemo
----------------

The **RECOMMENDED** way to install Mnemo is using the PEAR installer.
Alternatively, if you want to run the latest development code or get the
latest not yet released fixes, you can install Mnemo from Git.

### Installing with PEAR

First follow the instructions in [horde/doc/INSTALL][README] to prepare
a PEAR environment for Horde and install the Horde Framework.

When installing Mnemo through PEAR now, the installer will automatically
install any dependencies of Mnemo too. If you want to install Mnemo with
all optional dependencies, but without the binary PECL packages that
need to be compiled, specify both the `-a` and the `-B` flag:

    pear install -a -B horde/mnemo

By default, only the required dependencies will be installed:

    pear install horde/mnemo

If you want to install Mnemo even with all binary dependencies, you need
to remove the `-B` flag. Please note that this might also try to install
PHP extensions through PECL that might need further configuration or
activation in your PHP configuration:

    pear install -a horde/mnemo

### Installing from Git

See <http://www.horde.org/source/git.php>

Configuring Mnemo
-----------------

1.  Configuring Horde for Mnemo

    Mnemo requires a permanent `Shares` backend in Horde to manage
    notepads and to add notes to notepads. If you didn\'t setup a Share
    backend yet, go to the configuration interface, select Horde from
    the list of applications and select the `Shares` tab. Unless you are
    using Kolab, you should select \`\`

  [README]: 