[![Symcon Module](https://img.shields.io/badge/Symcon-PHPModul-blue.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
![Symcon Version](https://img.shields.io/badge/dynamic/json?color=blue&label=Symcon%20Version&prefix=%3E%3D&query=compatibility.version&url=https%3A%2F%2Fraw.githubusercontent.com%2Froastedelectrons%2FEnergiesparrechner%2Fmain%2Flibrary.json)
![Module Version](https://img.shields.io/badge/dynamic/json?color=green&label=Module%20Version&query=version&url=https%3A%2F%2Fraw.githubusercontent.com%2Froastedelectrons%2FEnergiesparrechner%2Fmain%2Flibrary.json)
![GitHub](https://img.shields.io/github/license/roastedelectrons/Energiesparrechner)

# Energiesparrechner
Modul für IP-Symcon zum witterungsbereinigten Vergleich des Energieverbrauchs eines Zeitraums mit dem Verbrauch aus dem vorherigen oder Vorjahreszeitraum. Es stehen verschiedene Möglichkeiten bereit, den Zeitraum zu definieren:
 1. Von Datum zu Datum
 2. Von Datum zum aktuellen Tag
 3. Gleitender Zeitraum über die letzten X Tage 

Der Energieverbrauch kann witterungsbereinigt werden, um z.B. Heizenergieverbräuche besser vergleichen zu können. Für die Witterungsbereinigung ist eine Variable der Außentemperatur mit aktiviertem Logging erforderlich. Die Witterungsbereinigung erfolgt über die Berechnung der [Gradtagszahlen nach VDI 2067](https://de.wikipedia.org/wiki/Gradtagzahl). Der aktuelle Zeitraum ist der Referenzzeitraum, die Energieverbräuche der zurückliegenden Zeiträume werden witterungsbereinigt.


### Inhaltsverzeichnis

1. [Voraussetzungen](#1-voraussetzungen)
2. [Enthaltene Module](#2-enthaltene-module)
3. [Installation](#3-software-installation)
4. [Einrichtung in IP-Symcon](#4-einrichtung-in-ip-symcon)
5. [Lizenz](#5-lizenz)

### 1. Voraussetzungen

- IP-Symcon ab Version 6.0

### 2. Enthaltene Module

- __Energiesparrechner__ ([Dokumentation](Energiesparrechner))  


### 3. Installation

In IP-Symcon über den Module Store das 'Energiesparrechner'-Modul installieren.

### 4. Einrichtung in IP-Symcon

Zur Konfiguration des Moduls siehe Modul-Dokumentation.

### 5. Lizenz
MIT License

Copyright (c) 2022 Tobias Ohrdes