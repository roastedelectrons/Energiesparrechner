# Energiesparrechner
Mit dem Energiesparrechner kann der Energieverbrauch eines Zeitraums mit dem Verbrauch aus dem vorherigen oder Vorjahreszeitraum verglichen werden. Es stehen verschiedene Möglichkeiten bereit, den Zeitraum zu definieren (mit Datumsauswahl oder gleitend). Der Energieverbrauch kann witterungsbereinigt werden, um z.B. Heizenergieverbräuche besser vergleichen zu können. Für die Witterungsbereinigung ist eine Variable der Außentemperatur mit aktivitem Logging erforderlich. Die Witterungsbereinigung erfolgt dabei über die Berechnung der Gradtagszahlen in den jeweiligen Vergleichszeiträumen.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Vergleich des Energieverbrauchs unterschiedlicher Zeiträume
* Witterungsbereinigung
* Verschiedene Zeitraumdefinitionen möglich: 
  * Von Datum zu Datum
  * Von Datum zum aktuellen Tag
  * Gleitender Zeitraum über die letzten X Tage

### 2. Voraussetzungen

- IP-Symcon ab Version 6.0

### 3. Software-Installation

* Über den Module Store das 'Energiesparrechner'-Modul installieren.
* Alternativ über das Module Control folgende URL hinzufügen [Github Repository](https://github.com/roastedelectrons/Energiesparrechner)

### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'Energiesparrechner'-Modul mithilfe des Schnellfilters gefunden werden.  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
MeterVariableID             | Zählervariable deren Zeiträume verglichen werden sollen. Typ: Float oder Integer. Aggregationstyp: Zähler
TemperatureVariableID       | Variable, in der die Außentemperaturen geloggt werden. 
PeriodType                  | Auswahl, wie die Zeiträume definiert werden (Datum, Tage)
Variables                   | Auswahl der anzulegenden Variablen mit verschiedenen Kenngrößen und Vergleichszeiträumen
UpdateInteral               | Interval in dem die Berechnungen durchgeführt werden (in Stunden)


### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

#### Statusvariablen
Die folgenden Variablen dienen der Bedienung des Moduls. Sie werden in Abhängigkeit des Periodentyps angelegt.

Ident   | Typ     | Beschreibung
------ | ------- | ------------
StartDate    |  Integer      | Auswahl des Beginn des Zeitraums (Bei Periodentypen: 1. Datum bis Datum, 2. Datum bis heute)
EndDate      |  Integer      | Auswahl des Ende des Zeitraums (Bei Periodentypen: 1. Datum bis Datum)
PeriodLength |  Integer      | Auswahl der Zeitraumlänge in Tagen (Bei Periodentyp: 3. Gleitender Zeitraum)

Die foldenden Variablen enhalten die berechneten Kennwerte. Im Konfigurationsformular kann für jede Variable definiert werden, ob sie angelgt werden soll.
Ident   | Typ     | Beschreibung
------ | ------- | ------------
EnergyCurrentPeriod | Float | aktueller Zeitraum
EnergyLastPeriod | Float | vorheriger Zeitraum
EnergyLastPeriodCorrected | Float | vorheriger Zeitraum (witterungsbereinigt)
EnergyLastYearsPeriod | Float | Vorjahreszeitraum 
EnergyLastYearsPeriodCorrected | Float | Vorjahreszeitraum (witterungsbereinigt)
PercentLastPeriod | Float | Vergleich zum vorherigen Zeitraum
PercentLastPeriodCorrected | Float | Vergleich zum vorherigen Zeitraum (witterungsbereinigt)
PercentLastYearsPeriod | Float | Vergleich zum Vorjahreszeitraum
PercentLastYearsPeriodCorrected | Float | Vergleich zum Vorjahreszeitraum (witterungsbereinigt)
DegreeDaysCurrentPeriod | Float | Gradtagszahl aktueller Zeitraum
DegreeDaysLastPeriod | Float | Gradtagszahl vorheriger Zeitraum
DegreeDaysLastYearsPeriod | Float | Gradtagszahl Vorjahreszeitraum


#### Profile

Name   | Typ
------ | -------
ESR.DegreeDays | Float
ESR.Days       | Integer

### 6. WebFront

Im WebFront kann der Zeitraum je nach Periodentyp verändert werden. Alle berechneten Kennwerte können im Webfront angezeigt werden.

### 7. PHP-Befehlsreferenz

`boolean ESR_Update(integer $InstanzID);`
Führt die Brechnung der Kennzahlen aus.

Beispiel:
`ESR_Update(12345);`

`boolean ESR_ResetVariables(integer $InstanzID);`
Setzt die Einstellungen der Variablen auf Standard zurück. Dies kann ggf. bei funktionalen Updates erforderlich sein.

Beispiel:
`ESR_ResetVariables(12345);`