# -*- encoding: utf-8 -*-
from httplib2 import Http
import urllib
import urllib2
import browser_cookie
from bs4 import  BeautifulSoup
import xlwt
from datetime import datetime
import csv
import os
#import requests

date_day = datetime.now().date()
#Setting up output folder
try:
    os.stat("/opt/scripts/movies/output/")
except:
    os.mkdir("/opt/scripts/movies/output/")
os.chdir("/opt/scripts/movies/output/")
try:
    os.stat("/opt/scripts/movies/output/"+str(date_day)+"/")
except:
    os.mkdir(str(date_day))
os.chdir("/opt/scripts/movies/output/"+str(date_day)+"/")
#Get number of pages
nb_pages = 0
hdr = {'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11',
       'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
       'Accept-Charset': 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
       'Accept-Encoding': 'none',
       'Accept-Language': 'en-US,en;q=0.8',
       'Connection': 'keep-alive'}
req = "http://www.dpstream.net/films"
try :
    request = urllib2.Request(req, headers=hdr)
    html = urllib2.urlopen(request).read()
except :
    print 'REQUEST ERROR'
    print req
soup = BeautifulSoup(html)
full_tags = soup.findAll("h3", {"class" : "moduleTitle pull-left"})
pages_string = ""
pages_string = full_tags[0].getText()
if pages_string:
    pages_string = pages_string.split(' ')
    nb_pages = int(pages_string[-1])
print "Number of pages = ",nb_pages
#nb_pages = 1
#préparation du fichier CSV
print "Préparation du fichier CSV ................."
#CSV FOR CATEGORIES
f = open("dps_movies_db_categories_"+str(date_day)+".csv", "wb")
csv_file_categories = csv.writer(f,delimiter=';')
csv_file_categories.writerow(["ID","NAME","TYPE"])
full_tags = soup.findAll("select", {"id" : "MovieCategorie"})
categ_row_id = 0
categs_tab = []
for option in full_tags[0].findAll("option"):
    if categ_row_id >= 1:
        categ_txt = option.getText().strip()
        categ_id = str(option['value']).strip()
        categs_tab.append({'id':categ_id,'name':categ_txt})
        csv_file_categories.writerow([categ_id,str(categ_txt),"movie"])
    categ_row_id+=1
f.close()
#CSV FOR MOVIES
csv_file = csv.writer(open("dps_movies_db_"+str(date_day)+".csv", "wb"),delimiter=';')
csv_file.writerow(["ROW_ID","TITLE","ALT TITLE","RLS DATE","YEAR","DURATION (MIN)","DIRECTORS","ACTORS","CATEGORIES","ORIGIN","DESCRIPTION","IMAGE","KEYWORDS"])
#CSV FOR LINKS
csv_file_links = csv.writer(open("dps_movies_db_links_"+str(date_day)+".csv", "a"),delimiter=';')
csv_file_links.writerow(["MOVIE_TITLE","MOVIE_ROW_ID","PLAYER","VERSION","QUALITY","LINK"])
movie_row_id=0
for i in range(nb_pages):
    print "Parsing page : ",i+1,"/",nb_pages
    req = "http://www.dpstream.net/films-recherche?page="+str(i+1)
    try :
        request = urllib2.Request(req, headers=hdr)
        html = urllib2.urlopen(request).read()
    except :
        print 'REQUEST ERROR'
        print req
    soup = BeautifulSoup(html)
    full_tags = soup.findAll("h3", {"class" : "resultTitle uppercaseLetter pull-left"})
    for each_tag in full_tags:
        movie_row_id+=1
        row= {}
        ref_tag = each_tag.find_all("a")
        movie_page = ref_tag[0].get('href')
        req = "http://www.dpstream.net"+str(movie_page)
        try :
            request = urllib2.Request(req, headers=hdr)
            html = urllib2.urlopen(request).read()
        except :
            print 'REQUEST ERROR'
            print req
        soup = BeautifulSoup(html)
        #TITLE
        tags = soup.findAll("span", {"class" : "tv_title"})
        if tags:
            row['title'] = tags[0].getText()
            #print row['title']
	    row['title'].replace(".","")
        #ALTERNATIVE TITLE
        tags = soup.findAll("span", {"itemprop" : "alternativeHeadline"})
        if tags:
            row['a_title'] = tags[0].getText()
	    row['a_title'].replace(".","")	    
        else:
            row['a_title'] = "ND"
        #RELEASE DATE
        tags = soup.findAll("div", {"class" : "tv_status"})
        if tags:
            rls_date = tags[0].getText()
            if len(rls_date) > 5 :
                rls_date = rls_date.split(':')
                row['rls_date'] = rls_date[-1].strip()
            else:
                row['rls_date'] = "ND"
        #INFOS
        tags = soup.findAll("div", {"class" : "content-right"})
        if tags:
            year_duration_csa = tags[0].find_all("div")
            #YEAR
            year = year_duration_csa[0].find_all("b")
            row['year'] = year and year[0].getText() or "ND"
            #DURATION
            duration = year_duration_csa[1].find_all("b")
            row['duration'] = duration and duration[0].getText() or "ND"
            if row['duration'] != "ND":
                duration = row['duration'].split(' ')
                row['duration'] = duration[0]
        #DIRECTORS
        tags = soup.findAll("span", {"itemprop" : "director"})
        row['directors'] = ""
        for tag in tags:
            directors = tag.findAll("span", {"itemprop" : "name"})
            row['directors'] += directors and directors[0].getText() or "ND"
            row['directors']+=", "
        if row['directors'] != "": row['directors'] = row['directors'][:-2]
        #actors
        tags = soup.findAll("span", {"itemprop" : "actor"})
        row['actors'] = ""
        for tag in tags:
            actors = tag.findAll("span", {"itemprop" : "name"})
            if actors and actors[0].getText() not in row['actors']:
	    	row['actors'] += actors and actors[0].getText() or "ND"
            	row['actors']+=", "
        if row['actors'] != "": row['actors'] = row['actors'][:-2]
        #CATEGORIES
        tags = soup.findAll("span", {"itemprop" : "genre"})
        row['categories'] = ""
        for tag in tags:
            categories = tag.findAll("span", {"itemprop" : "name"})
            categ_txt2 = categories and categories[0].getText().strip() or "ND"
            for c in categs_tab:
                if c['name'] == categ_txt2:
                    row['categories'] += str(c['id']).strip()
                    row['categories']+=","
        if row['categories'] != "": row['categories'] = row['categories'][:-1]
        #COUNTRYOFORIGIN
        tags = soup.findAll("span", {"itemprop" : "countryOfOrigin"})
        row['countryoforigin'] = ""
        for tag in tags:
            countryoforigin = tag.findAll("span", {"itemprop" : "name"})
            row['countryoforigin'] += countryoforigin and countryoforigin[0].getText() or "ND"
            row['countryoforigin']+=","
        if row['countryoforigin'] != "": row['countryoforigin'] = row['countryoforigin'][:-1]
        #DESCRIPTION
        description = soup.findAll("meta", {"property" : "og:description"})
        row['description']= "ND"
        if description:
            row['description'] = description[0]['content']
        #IMAGE
        row['image']="ND"
        image = soup.findAll("img", {"itemprop" : "image"})
        if image:
            row['image'] = image[0]['src']
            if row['image'][:8] == '//static':row['image'] = "http:"+row['image']
        #CHECK IMAGE ERROR
        #row['image_error'] = 0
        #img_request = requests.get(row['image'])
        #if img_request.status_code != 200:
        #    row['image_error'] = 1
        #KEYWORDS
        keywords = soup.findAll("meta", {"name" : "keywords"})
        row['keywords']= "ND"
        if keywords:
            row['keywords'] = keywords[0]['content']
        #LINKS
        for number in ['0','1','2','3','4','5','6','7','8','9','10']:
            tr_nodes = soup.findAll("tr", {"id" : str(number)})
            for tr in tr_nodes:
                td_nodes = tr.find_all("td")
                a_nodes = td_nodes[5].find_all("a")
                link_row={
                     'movie_title':row['title'],
                     'movie_row_id':movie_row_id,
                     'player':td_nodes and td_nodes[0].getText() or 'ND',
                     'version':td_nodes and td_nodes[1].getText() or 'ND',
                     'quality':td_nodes and td_nodes[2].getText() or 'ND',
                     'link':a_nodes and a_nodes[0].get("href") or "ND",
                     }
                csv_file_links.writerow([str(link_row['movie_title']).strip(),link_row['movie_row_id'],
                                   str(link_row['player']).strip(),str(link_row['version']).strip(),
                                   str(link_row['quality']).strip(),str(link_row['link']).strip()
                                   ])

        csv_file.writerow([movie_row_id,str(row['title']).strip(),str(row['a_title']).strip(),str(row['rls_date']).strip(),
                           str(row['year']).strip(),str(row['duration']).strip(),str(row['directors']).strip(),
                           str(row['actors']).strip(),str(row['categories']).strip(),str(row['countryoforigin']).strip(),
                           str(row['description']).strip(),str(row['image']).strip(),row['keywords'].strip()])
