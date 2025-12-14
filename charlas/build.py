import datetime

import yaml
from jinja2 import Template

_MONTHS = "Ene Feb Mar Abr May Jun Jul Ago Sep Oct Nov Dic"
MONTHS = {month: f"{pos:02d}" for pos, month in enumerate(_MONTHS.split(), 1)}


with open("charlas.yaml", "rt") as fh:
    data = yaml.safe_load(fh)

# group by talk
alltalks = {}
for datum in data:
    alltalks.setdefault(datum["title"], []).append(datum)

# parse date so can be ordered
for title, data in alltalks.items():
    for datum in data:
        monthname, year = datum["date"].split()
        assert year.isdigit()
        monthnum = MONTHS[monthname]
        datum["orddate"] = f"{year}-{monthnum}"

ordered = sorted(
    alltalks.items(),
    key=lambda it: max(datum["orddate"] for datum in it[1]),
    reverse=True
)

context = {}
context["current_year"] = datetime.date.today().year

talks = context["talks"] = []
for title, data in ordered:
    data = alltalks[title]
    talk = {}
    talks.append(talk)

    talk["title"] = title
    talk["events"] = events = []

    grouped = {}
    for datum in data:
        grouped.setdefault((datum["event"], datum["location"]), []).append(datum["date"])

    for (name, location), dates in grouped.items():
        *first_dates, last_date = dates
        if first_dates:
            joined_dates = ", ".join(first_dates) + " y " + last_date
        else:
            joined_dates = last_date
        events.append(
            {
                "name": name,
                "location": location,
                "date": joined_dates,
            }
        )

with open("template.html", "rt") as fh:
    tmpl = Template(fh.read())

output = tmpl.render(context)
with open("charlas.html", "wt") as fh:
    fh.write(output)
