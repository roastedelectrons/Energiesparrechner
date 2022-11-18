# Energiesparrechner

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Konfiguration](#2-konfiguration)
3. [Statusvariablen und Profile](#3-statusvariablen-und-profile)
4. [WebFront](#4-webfront)
5. [PHP-Befehlsreferenz](#5-php-befehlsreferenz)

### 1. Funktionsumfang

* Vergleich des Energieverbrauchs unterschiedlicher Zeiträume
* Witterungsbereinigung
* Verschiedene Zeitraumdefinitionen möglich: 
  * Von Datum zu Datum
  * Von Datum zum aktuellen Tag
  * Gleitender Zeitraum über die letzten X Tage

### 2. Konfiguration

__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
MeterVariableID             | Zählervariable deren Zeiträume verglichen werden sollen. Typ: Float oder Integer. Aggregationstyp: Zähler
TemperatureVariableID       | Variable, in der die Außentemperaturen geloggt werden. 
PeriodType                  | Auswahl, wie die Zeiträume definiert werden (Datum, Tage)
Variables                   | Auswahl der anzulegenden Variablen mit verschiedenen Kenngrößen und Vergleichszeiträumen
UpdateInteral               | Interval in dem die Berechnungen durchgeführt werden (in Stunden)


### 3. Statusvariablen und Profile

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

### 4. WebFront

Im WebFront kann der Zeitraum je nach Periodentyp verändert werden. Alle berechneten Kennwerte können im Webfront angezeigt werden.

### 5. PHP-Befehlsreferenz

`boolean ESR_Update(integer $InstanzID);`
Führt die Brechnung der Kennzahlen aus.

`boolean ESR_ResetVariableList(integer $InstanzID);`
Setzt die Einstellungen der Variablen auf Standardeinstellungen zurück. Hierbei werden auch die Namen der existierenden Variablen umbenannt.

`boolean ESR_ResetUpdateList(integer $InstanzID);`
Aktualisiert die Variablenliste. Dies kann erforderlich sein, wenn durch ein Modul Update neu Statusvariablen hinzugefügt oder entfernt wurden.