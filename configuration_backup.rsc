# jan/28/2025 00:36:01 by RouterOS 6.49.17
# software id = LEWM-GR0E
#
# model = 951Ui-2HnD
# serial number = HHE0AA0YVNY
/interface bridge
add admin-mac=F4:1E:57:8B:BB:3A auto-mac=no comment=defconf name=bridge
add name=bridge-hotspot
/interface ovpn-client
add add-default-route=yes comment=ovpn connect-to=sg-10.hostddns.us \
    mac-address=FE:B7:C4:7F:05:CC name=ovpn-client password=12345678 user=\
    admintest2@mytunnel.id
/interface wireless
set [ find default-name=wlan1 ] band=2ghz-b/g/n channel-width=20/40mhz-Ce \
    disabled=no distance=indoors frequency=auto installation=indoor mode=\
    ap-bridge ssid="Surf Hotspot" wireless-protocol=802.11
/interface list
add comment=defconf name=WAN
add comment=defconf name=LAN
/interface wireless security-profiles
set [ find default=yes ] authentication-types=wpa2-psk comment=defconf \
    disable-pmkid=yes supplicant-identity=MikroTik wpa2-pre-shared-key=\
    IBHKB7ADSH
/ip hotspot profile
add dns-name=surf.co.ke hotspot-address=192.168.6.1 login-by=http-chap name=\
    hsprof1 use-radius=yes
/ip pool
add name=default-dhcp ranges=192.168.88.10-192.168.88.254
add name=hotspot-pool ranges=192.168.6.10-192.168.6.254
add name=dhcp ranges=192.168.88.10-192.168.88.254
/ip dhcp-server
add address-pool=default-dhcp disabled=no interface=bridge name=default
add address-pool=hotspot-pool disabled=no interface=bridge-hotspot \
    lease-time=1h name=dhcp1
/ip hotspot
add address-pool=hotspot-pool addresses-per-mac=1 disabled=no interface=\
    bridge-hotspot name=hotspot1 profile=hsprof1
/ip hotspot user profile
add address-pool=hotspot-pool name=quick30 parent-queue=none rate-limit=5M/5M
/tool user-manager customer
set admin access=\
    own-routers,own-users,own-profiles,own-limits,config-payment-gw password=\
    admin
/tool user-manager profile
add name=brone name-for-users="" override-shared-users=off owner=admin price=\
    100 starts-at=logon validity=0s
/tool user-manager profile limitation
add address-list="" download-limit=0B group-name="" ip-pool="" ip-pool6="" \
    name="bronze " owner=admin rate-limit-min-rx=131072B rate-limit-min-tx=\
    1048576B rate-limit-rx=131072B rate-limit-tx=1048576B transfer-limit=0B \
    upload-limit=0B uptime-limit=0s
/interface bridge port
add bridge=bridge-hotspot comment=defconf interface=ether2
add bridge=bridge comment=defconf interface=ether3
add bridge=bridge comment=defconf interface=ether4
add bridge=bridge comment=defconf interface=ether1
add bridge=bridge-hotspot comment=defconf interface=wlan1
/ip neighbor discovery-settings
set discover-interface-list=all
/interface detect-internet
set detect-interface-list=all
/interface list member
add comment=defconf interface=bridge-hotspot list=LAN
add interface=ether5 list=WAN
add interface=ovpn-client list=LAN
/interface ovpn-server server
set auth=sha1 certificate=server-certificate cipher=aes128
/ip address
add address=192.168.88.1/24 comment=defconf interface=bridge network=\
    192.168.88.0
add address=192.168.6.1/24 comment="hotspot network" interface=bridge-hotspot \
    network=192.168.6.0
/ip dhcp-client
add comment=defconf disabled=no interface=ether5
/ip dhcp-server network
add address=192.168.6.0/24 comment="hotspot network" gateway=192.168.6.1
add address=192.168.88.0/24 comment=defconf dns-server=192.168.88.1 gateway=\
    192.168.88.1 netmask=24
/ip dns
set allow-remote-requests=yes servers=8.8.8.8
/ip dns static
add address=192.168.88.1 comment=defconf name=router.lan
/ip firewall filter
add action=passthrough chain=unused-hs-chain comment=\
    "place hotspot rules here" disabled=yes
add action=accept chain=input comment=\
    "defconf: accept established,related,untracked" connection-state=\
    established,related,untracked
add action=drop chain=input comment="defconf: drop invalid" connection-state=\
    invalid
add action=accept chain=input comment="defconf: accept ICMP" protocol=icmp
add action=accept chain=input comment=\
    "defconf: accept to local loopback (for CAPsMAN)" dst-address=127.0.0.1
add action=drop chain=input comment="defconf: drop all not coming from LAN" \
    in-interface-list=!LAN
add action=accept chain=forward comment="defconf: accept in ipsec policy" \
    ipsec-policy=in,ipsec
add action=accept chain=forward comment="defconf: accept out ipsec policy" \
    ipsec-policy=out,ipsec
add action=fasttrack-connection chain=forward comment="defconf: fasttrack" \
    connection-state=established,related
add action=accept chain=forward comment=\
    "defconf: accept established,related, untracked" connection-state=\
    established,related,untracked
add action=drop chain=forward comment="defconf: drop invalid" \
    connection-state=invalid
add action=drop chain=forward comment=\
    "defconf: drop all from WAN not DSTNATed" connection-nat-state=!dstnat \
    connection-state=new in-interface-list=WAN
add action=accept chain=input comment="Allow OpenVPN TCP 13575" dst-port=8728 \
    protocol=tcp
add action=accept chain=input dst-port=1194 protocol=tcp
add action=accept chain=input dst-port=8728 protocol=tcp src-address=\
    0.0.0.0/0
add action=accept chain=input in-interface=ovpn-client
add action=accept chain=input dst-port=8728 protocol=tcp src-address=\
    10.10.224.105
add action=accept chain=input dst-port=8728 protocol=tcp src-address=\
    10.8.0.0/24
add action=accept chain=input protocol=icmp
add action=accept chain=input dst-port=8728 protocol=tcp src-address=\
    10.8.0.0/24
add action=accept chain=input protocol=icmp src-address=10.8.0.0/24
add action=accept chain=input protocol=icmp src-address=10.8.0.0/24
add action=accept chain=input dst-port=8728 protocol=tcp src-address=\
    10.8.0.0/24
add action=accept chain=input in-interface=ovpn-client
add action=accept chain=forward in-interface=ovpn-client
add action=accept chain=forward dst-address=10.8.0.0/24 src-address=\
    10.10.224.0/24
add action=accept chain=forward dst-address=10.10.224.0/24 src-address=\
    10.8.0.0/24
/ip firewall nat
add action=passthrough chain=unused-hs-chain comment=\
    "place hotspot rules here" disabled=yes
add action=masquerade chain=srcnat comment="defconf: masquerade" \
    ipsec-policy=out,none out-interface-list=WAN
add action=masquerade chain=srcnat comment="masquerade hotspot network" \
    src-address=192.168.6.0/24
add action=masquerade chain=srcnat comment="masquerade hotspot network" \
    src-address=192.168.6.0/24
add action=masquerade chain=srcnat out-interface-list=WAN
add action=masquerade chain=srcnat out-interface=ovpn-client
add action=masquerade chain=srcnat dst-address=10.8.0.0/24 src-address=\
    10.10.224.0/24
/ip hotspot user
add name=admin password=admin
add limit-uptime=5m name=user1 password=user1
add limit-uptime=5m name=user2 password=user2
/ip route
add distance=1 dst-address=10.8.0.0/24 gateway=ovpn-client
/ip service
set www-ssl disabled=no
set api address=0.0.0.0/0
/radius
add address=127.0.0.1 secret=1234 service=hotspot
/system clock
set time-zone-name=Asia/Singapore
/system logging
add action=disk prefix=-> topics=hotspot,info,debug
add topics=ovpn,debug
/tool user-manager database
set db-path=user-manager
/tool user-manager profile profile-limitation
add from-time=0s limitation="bronze " profile=brone till-time=23h59m59s \
    weekdays=sunday,monday,tuesday,wednesday,thursday,friday,saturday
/tool user-manager router
add coa-port=1700 customer=admin disabled=no ip-address=127.0.0.1 log=\
    auth-fail name="hotspot " shared-secret=admin use-coa=no
/tool user-manager user
add customer=admin disabled=no ipv6-dns=:: password=admin shared-users=1 \
    username=admin wireless-enc-algo=none wireless-enc-key="" wireless-psk=""
add customer=admin disabled=no ipv6-dns=:: password=test shared-users=1 \
    username=test wireless-enc-algo=none wireless-enc-key="" wireless-psk=""
