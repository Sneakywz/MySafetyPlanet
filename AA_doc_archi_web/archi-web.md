# SERVEUR APACHE, HEBERGEMENT ET SECURITE

Afin que le site web soit accessible et visible, un serveur web doit être déployé. C’est à travers APACHE que nous déployons MySafetyPlanet. Cependant, il est fondamental d’établir une architecture réseau pour la création du serveur.

## 1.1	L’architecture réseau
Le serveur apache, installé sur une machine virtuelle sous debian 12, fonctionne dans un environnement de virtualisation nommé Proxmox. Il s’agit d’un hyperviseur de niveau 1, permettant le déploiement simultané de plusieurs VM. Proxmox se trouve ici sur une machine physique Fujitsu. 

Cette dernière doit irrémédiablement être connectée en Ethernet. Cependant, une contrainte technique se présente à nous ; aucune sortie Ethernet n’est disponible physiquement, puisque le routeur du FAI n’est accessible que par une connexion Wi-Fi. Ainsi, nous devons adapter un système permettant la connexion Ethernet à travers un routeur intermédiaire. C’est ici qu’entre en jeu le Raspberry Pi 4B. Celui-ci est utilisé en mode routeur, afin de transmettre la connexion Wi-Fi, via son port Ethernet à un switch. Le switch transmet alors au serveur Proxmox, la connexion internet. 


 
![alt text](images/image.png)

<p style="color:red;"><em>Figure 1 - Schéma d'architecture réseau du serveur web</em></p>

## 1.2	La connectivité VPN
Afin d’accéder à distance au sous réseau présent, une connexion VPN est établie. La technologie WireGuard permet de créer un tunnel sécurisé d’un client vers un serveur. Des règles Iptables sont ajoutées, permettant d’activer et de désactiver le transfert de paquet via la règle « MASQUERADE ».
```ini
[Interface]
Address = 10.0.0.1/24
ListenPort = 51820
PrivateKey = <clé privée masquée>

PostUp = iptables -A FORWARD -i wg0 -j ACCEPT; iptables -t nat -A POSTROUTING -o wlan0 -j MASQUERADE
PostDown = iptables -D FORWARD -i wg0 -j ACCEPT; iptables -t nat -D POSTROUTING -o wlan0 -j MASQUERADE

[Peer]
PublicKey = <clé publique masquée>
AllowedIPs = 10.0.0.2/32
```

<p style="color:red;"><em>Figure 2 - Fichier de configuration serveur VPN</em></p>

![alt text](images/image-2.png)

<p style="color:red;"><em>Figure 3 - Configuration du client VPN</em></p>

Malgré tout, une problématique reste à traiter : si l’adresse IP du routeur du FAI change (ce qui est fréquent), il faut que la connexion soit persistante. DuckDNS est un service permettant de relier une adresse IP publique à un nom de domaine. De ce fait, lorsque l’adresse IP change, le nom de domaine est mis à jour avec la nouvelle IP. La connexion n’est alors plus liée directement à l’IP mais au nom de domaine généré par DuckDNS.

![alt text](images/image-3.png)

<p style="color:red;"><em>Figure 4 - Page DuckDNS</em></p>

Le serveur prend donc comme paramètre le nom de domaine de DuckDNS. Malgré tout, une actualisation manuelle doit être réalisée, si le nom de domaine transcrit une IP différente de l’origine. Pour automatiser ce processus, nous avons créé un script. Celui-ci permet d’actualiser les paramètres en question.
```bash
#!/bin/bash
curl -s "https://www.duckdns.org/update?domains=<domaine>&token=<token>&ip=" >> ~/duckdns/duck.log
echo "" >> ~/duckdns/duck.log
```

<p style="color:red;"><em>Figure 5 - Script Bash d'actualisation DuckDNS</em></p>

```bash
# Edit this file to introduce tasks to be run by cron.

# Each task to run has to be defined through a single line
# indicating with different fields when the task will be run
# and what command to run for the task

# To define the time you can provide concrete values for
# minute (m), hour (h), day of month (dom), month (mon),
# and day of week (dow) or use '*' in these fields (for 'any').

# Notice that tasks will be started based on the cron's system
# daemon's notion of time and timezones.

# Output of the crontab jobs (including errors) is sent through
# email to the user the crontab file belongs to (unless redirected).

# Example:
# Run a backup at 5:00 a.m. every week:
# 0 5 * * 1 tar -zcf /var/backups/home.tgz /home/

# m  h  dom mon dow  command
*/5 * * * * /root/duckdns/duck.sh >/dev/null 2>&1
```

<p style="color:red;"><em>Figure 6 - Exécution du script précédent toutes les cinq minutes</em></p>

## 1.3	Interface Proxmox
L’architecture étant désormais opérationnelle, l’interface web de gestion de l’hyperviseur Proxmox est donc accessible. Elle présente des graphiques de performances et d’interface complète pour la gestion des VM.

![alt text](images/image-6.png)

<p style="color:red;"><em>Figure 7 - Interface Proxmox</em></p>

## 1.4	Le serveur APACHE
Le serveur APACHE est installé sur une VM sous debian 12. Nous avons choisi cette version de Linux, pour sa stabilité et sa gestion efficace. 

L’élément principal du site web hébergé sur le serveur est son fichier .conf. Celui-ci se trouve dans le répertoire 
```/etc/apache2/sites-available```. Il présente l’ensemble des configurations nécessaire au déploiement. Ici, on identifie plusieurs paramètres ; le chemin d’accès au dossier contenant le code source du site web, ainsi qu’une section HTTP et HTTPS avec une redirection automatique du premier vers le deuxième. Celui-ci implique la génération de certificats SSL auto-signés. 
Enfin, une résolution DNS est appliquée sur la machine serveur et sur la machine client, afin de pouvoir y accéder par le nom de domaine **« mysafetyplanet.local »**.

```apache
# Configuration du VirtualHost HTTP
<VirtualHost *:80>
    ServerName mysafetyplanet.local
    Redirect permanent / https://mysafetyplanet.local
    DocumentRoot /var/www/mysafetyplanet.local

    ErrorLog ${APACHE_LOG_DIR}/mysafetyplanet_error.log
    CustomLog ${APACHE_LOG_DIR}/mysafetyplanet_access.log combined
</VirtualHost>

# Configuration du VirtualHost HTTPS
<VirtualHost *:443>
    ServerName mysafetyplanet.local
    DocumentRoot /var/www/mysafetyplanet.local

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/mysafetyplanet.local.crt
    SSLCertificateKeyFile /etc/ssl/private/mysafetyplanet.local.key

    <Directory /var/www/mysafetyplanet.local>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/mysafetyplanet_error.log
    CustomLog ${APACHE_LOG_DIR}/mysafetyplanet_access.log combined
</VirtualHost>
```
<p style="color:red;"><em>Figure 8 - Fichier projet_annuel.conf</em></p>

![alt text](images/image-8.png)

<p style="color:red;"><em>Figure 9 – Outil de développement Navigateur : Redirection HTTP -> HTTPS</em></p>

```bash
root@My-Safety-Planet:~# curl -I http://mysafetyplanet.local
HTTP/1.1 301 Moved Permanently
Date: Wed, 28 May 2025 22:54:26 GMT
Server: Apache/2.4.62 (Debian)
Location: https://mysafetyplanet.local
Content-Type: text/html; charset=iso-8859-1
```


<p style="color:red;"><em>Figure 10 - Terminal : Redirection HTTP -> HTTPS</em></p>

```bash
/etc/ssl/private/mysafetyplanet.local.key
-----BEGIN PRIVATE KEY-----
MIIVEvIBAADGkqhkgiNg9wQBAQEFAASCBSXwggISAQAoIBAQDgg3z/7o0/c
...
qmb0/OGVA0iiuvfQq5Sy+8M5a2im9n6Cn9C71nJ06KEXETC6m95Uz1foqEZJYH4Vock
-----END PRIVATE KEY-----
```


<p style="color:red;"><em>Figure 11 - Clé SSL</em></p>

```bash
/etc/ssl/certs/mysafetyplanet.local.crt
-----BEGIN CERTIFICATE-----
MIIDnzCCAcegAwIBAgIUkl17mZtI8n6TW8tW8KTVExVRGbQkJxw0jQXThvcNAQEL
...
Yz6CQhR9d0P+8j8lwzNS4ZiKAwjhJx78jAqylErqtJQkXwRLN3e2NJJWDH7
-----END CERTIFICATE-----
```


<p style="color:red;"><em>Figure 12 - Certificat SSL</em></p>

```bash
# /etc/hosts

127.0.0.1       localhost
127.0.1.1       My-Safety-Planet
192.168.50.160  mysafetyplanet.local

# The following lines are desirable for IPv6 capable hosts
::1     localhost ip6-localhost ip6-loopback
ff02::1 ip6-allnodes
ff02::2 ip6-allrouters
```

<p style="color:red;"><em>Figure 13 - Résolution DNS Serveur</em></p>


```ini
# Copyright (c) 1993-2009 Microsoft Corp.

# This is a sample HOSTS file used by Microsoft TCP/IP for Windows.

# This file contains the mappings of IP addresses to host names. Each
# entry should be kept on an individual line. The IP address should
# be placed in the first column followed by the corresponding host name.
# The IP address and the host name should be separated by at least one
# space.

# Additionally, comments (such as these) may be inserted on individual
# lines or following the machine name denoted by a '#' symbol.

# For example:
#
#   102.54.94.97    rhino.acme.com          # source server
#   38.25.63.10     x.acme.com              # x client host

# localhost name resolution is handled within DNS itself.
#   127.0.0.1       localhost
#   ::1             localhost

192.168.50.157 monprojet.local
192.168.50.160 mysafetyplanet.local
```
<p style="color:red;"><em>Figure 14 - Résolution DNS Client</em></p>

## 1.5	Inclusion de la base de données
Avant l’activation du site, il nous reste à intégrer et connecter le serveur à sa base de données mysql.

```bash
root@My-Safety-Planet:/var/www/mysafetyplanet.local# nano src/Entity/Db.php
root@My-Safety-Planet:/var/www/mysafetyplanet.local# systemctl restart apache2
root@My-Safety-Planet:/var/www/mysafetyplanet.local# mariadb
Welcome to the MariaDB monitor.  Commands end with ; or \g.
Your MariaDB connection id is 49
Server version: 10.11.11-MariaDB-0+deb12u1 Debian 12

Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

MariaDB [(none)]> SHOW DATABASES;
+--------------------+
| Database           |
+--------------------+
| blog_cours         |
| information_schema |
| mysql              |
| performance_schema |
| sys                |
+--------------------+
5 rows in set (0,000 sec)

MariaDB [(none)]>
```
<p style="color:red;"><em>Figure 15 - Mariadb : liste des bases de données</em></p>

```sql
MariaDB [(none)]> USE blog_cours;
Reading table information for completion of table and column names
You can turn off this feature to get a quicker startup with -A

Database changed
MariaDB [blog_cours]> SHOW TABLES;
+----------------------+
| Tables_in_blog_cours |
+----------------------+
| users                |
+----------------------+
1 row in set (0,000 sec)

MariaDB [blog_cours]>
```                    
<p style="color:red;"><em>Figure 16 - Mariadb : liste des tables de la base "blog_cours"</em></p>

```sql
MariaDB [blog_cours]> DESCRIBE users;
+-------------+--------------+------+-----+---------+----------------+
| Field       | Type         | Null | Key | Default | Extra          |
+-------------+--------------+------+-----+---------+----------------+
| id          | int(11)      | NO   | PRI | NULL    | auto_increment |
| firstname   | varchar(255) | YES  |     | NULL    |                |
| lastname    | varchar(255) | YES  |     | NULL    |                |
| email       | varchar(255) | YES  |     | NULL    |                |
| password    | varchar(255) | YES  |     | NULL    |                |
| roles       | longtext     | NO   |     | NULL    |                |
| remember_me | varchar(255) | YES  |     | NULL    |                |
+-------------+--------------+------+-----+---------+----------------+
7 rows in set (0,001 sec)

MariaDB [blog_cours]> 
```
<p style="color:red;"><em>Figure 17 - Contenu de la table USERS</em></p>

## 1.6	Activation du site

Dès lors que l’ensemble de ces configurations sont mises en place, l’activation du site internet est réalisée.

```bash
root@My-Safety-Planet:~# ls /etc/apache2/sites-enabled/
projet_annuel.conf
```

<p style="color:red;"><em>Figure 18 - Activation du site internet</em></p>

## 1.7	Sécurisation
Afin de sécuriser les dossiers sensibles du serveur, des fichiers .htaccess sont édités. Ces fichiers définissent des règles d’accès à des fichiers spécifiques. Ici, le fichier admin.php n’est accessible que par authentification.
```apache
<Files "admin.php">
    AuthType Basic
    AuthName "Espace Sécurisé"
    AuthUserFile /etc/apache/.htpasswd
    Require valid-user
</Files>
```
<p style="color:red;"><em>Figure 19 - Fichier .htaccess ciblant admin.php</em></p>


```plaintext
maxime:$apr1$MKoSh4eo$uxtyw hJFGgMgzY3ZdZLBe.
lucas:$apr1$UOGHFpSP$jnVPEuQLjWDKywNcRZJax1
```

<p style="color:red;"><em>Figure 20 - Fichier d'authentification pour accéder à admin.php</em></p>


Enfin, en guise de premier audit de sécurité du site web, une énumération de Pentest est réalisée, afin d’identifier des failles initiales.

```bash
root@PenTest:~# nmap 192.168.50.200
Starting Nmap 7.95 ( https://nmap.org ) at 2025-05-30 19:27 CEST
Nmap scan report for 192.168.50.200
Host is up (0.0000030s latency).
Not shown: 998 closed tcp ports (reset)
PORT     STATE SERVICE
22/tcp   open  ssh
80/tcp   open  http

Nmap done: 1 IP address (1 host up) scanned in 0.14 seconds
root@PenTest:~# PORT 443 bloqué depuis l'extérieur car VM en NAT
```
<p style="color:red;"><em>Figure 21 - Nmap général</em></p>

Les résultats nous montrent plusieurs paramètres ; tout d’abord, il détecte le port 22 (SSH) et le port 80 (HTTP). 

```bash
root@PenTest:~# nmap -sV -p 80,443 192.168.50.200
Starting Nmap 7.95 ( https://nmap.org ) at 2025-05-30 19:02 CEST
Nmap scan report for 192.168.50.200
Host is up (0.000019s latency).

PORT    STATE  SERVICE  VERSION
80/tcp  open   http     Apache httpd 2.4.62 ((Debian))
443/tcp closed https

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
Nmap done: 1 IP address (1 host up) scanned in 6.34 seconds
root@PenTest:~#
```

<p style="color:red;"><em>Figure 22 - Nmap 80, 443</em></p>


Nous décidons d’effectuer un scan spécifique sur les ports du serveur web. Après plusieurs recherches, le fait que le serveur APACHE soit sur une machine virtuelle, le port 443 n’est pas visible, alors qu’effectivement actif.

```bash
root@PenTest:~# nikto -h http://192.168.50.200
- Nikto v2.5.0
---------------------------------------------------------------------------
+ Target IP:          192.168.50.200
+ Target Hostname:    192.168.50.200
+ Target Port:        80
+ Start Time:         2025-05-30 19:04:29 (GMT2)
---------------------------------------------------------------------------
+ Server: Apache/2.4.62 (Debian)
+ The anti-clickjacking X-Frame-Options header is not present. See: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options
+ The X-Content-Type-Options header is not set. This could allow the user agent to render 
  the content of the site in a different fashion to the MIME type. See: 
  https://www.netsparker.com/web-vulnerability-scanner/vulnerabilities/missing-content-type-header/
+ No CGI directories found (use '-C all' to force check all possible dirs)
+ Server may leak inodes via ETags, header found with file /, inode: 29cd, size: 6345e48643b17, mtime: gzip. See: http://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2003-1418
+ OPTIONS: Allows HTTP Methods: OPTIONS, HEAD, GET, POST 
+ /server-status: This reveals Apache information. Comment out appropriate line in the Apache 
  conf file or restrict access to allowed sources. See: OSVDB-561
+ 8102 requests: 0 error(s) and 5 item(s) reported on remote host
+ End Time:           2025-05-30 19:04:36 (GMT2) (7 seconds)
---------------------------------------------------------------------------
+ 1 host(s) tested

*************************************************************
Portions of the server's headers (Apache/2.4.62) are not in 
the Nikto 2.5.0 database or are newer than the known string. 
Would you like to submit this information (*no server specific data*) 
to CIRT.net for a Nikto update (or you may email to sullo@cirt.net) (y/n)? 
```

<p style="color:red;"><em>Figure 23 - Résultat de l'outil Nikto</em></p>

Un scan via l’outil NIKTO nous a permis d’identifier une faille. En effet, le résultat indique que certaines informations du serveur sont accessibles et implique une vulnérabilité potentielle. Cela induit qu’il sera nécessaire de vérifier plus en profondeur les différentes failles possibles, et les corriger si nécessaire. 

## 1.8	Remarques
Il est important de rappeler que le projet n’en est qu’à ses fondations. L’ensemble des éléments présentés ici pourraient et seront optimisés avant le déploiement officiel. Les aspects sécurité et optimisation de performances seront étudiées avec soin. Le nom de domaine devra aussi être acheté. Enfin, un hébergement sur un VPS est envisagé pour une meilleure stabilité sur le long terme.
