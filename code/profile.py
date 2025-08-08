import scholarly
import pandas as pd
import sys
from datetime import datetime


if len(sys.argv) != 2:
	print("Usage: profile.py \"author name\"")
	exit()

author_name = str(sys.argv[1])
print("Searching " + author_name + " on Google Scholar. This might take a while...")

search_query = scholarly.search_author(author_name)
author = next(search_query).fill()

print("Successfully retrieved Scholar profile.")

columns = ['cites', 'title',  'author', 'year', 'cites_per_year', 'eprint', 
'pages', 'publisher', 'url', 'id_citations', 'id_scholarcitedby', 'source', 'citedByUrl']

profile = pd.DataFrame(columns=columns)

num_pubs = len(author.publications)

print(author_name + " has " + str(num_pubs) + " papers on Scholar.")

for i in range(len(author.publications)):
	print("Processing pub " + str(i+1) + "/" + str(num_pubs))
	author.publications[i].fill()
	profile = profile.append(pd.Series([
	getattr(author.publications[i],'citedby', 0),
	author.publications[i].bib.get('title',""),
	author.publications[i].bib.get('author',""), 
	author.publications[i].bib.get('year',""),
	getattr(author.publications[i],'cites_per_year',""),
	author.publications[i].bib.get('eprint', ""),
	author.publications[i].bib.get('pages',""),
	author.publications[i].bib.get('publisher',""),
	author.publications[i].bib.get('url',""), 
	getattr(author.publications[i],'id_citations',""),
	getattr(author.publications[i],'id_scholarcitedby',""),
	getattr(author.publications[i],'source',""),
	"https://scholar.google.com/scholar?oi=bibs&hl=en&cites=" + getattr(author.publications[i],'id_scholarcitedby',"")
	], index=profile.columns), ignore_index=True)
	#print(profile.loc[i])

today = datetime.today().strftime('%Y%m%d')
filename = author_name.replace(" ", "") + "_" + today + ".csv"
print("Done. Saving to file... " + filename)
profile.to_csv(filename,index=False)
