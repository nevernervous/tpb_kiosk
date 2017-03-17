#!/bin/bash

# Run this script in display 0 - the monitor
export DISPLAY=:0

# Hide the mouse from the display
unclutter &

# If Chromium crashes (usually due to rebooting), clear the crash flag so we don't have the annoying warning bar
sed -i 's/"exited_cleanly":false/"exited_cleanly":true/' /home/kiosk/.config/chromium/Default/Preferences
sed -i 's/"exit_type":"Crashed"/"exit_type":"Normal"/' /home/kiosk/.config/chromium/Default/Preferences

# Run TeamViewer in foreground for persistent connectivity
/usr/bin/teamviewer &
# wait three seconds for TV to boot up, so that it stays behind Chromium
sleep 5

# Run Chromium and open tabs
/usr/bin/chromium-browser --kiosk --incognito  --disable-pinch --overscroll-history-navigation=0 http://the.peak.beyond &

# Start the kiosk loop. This keystroke changes the Chromium tab
# To have just anti-idle, use this line instead:
# Otherwise, the ctrl+Tab is designed to switch tabs in Chrome
# #
while (true)
  do
    xdotool keydown ctrl; xdotool keyup ctrl;
    sleep 15
done