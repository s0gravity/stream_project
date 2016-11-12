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
    os.stat("/opt/scripts/series/output/")
except:
    os.mkdir("/opt/scripts/series/output/")
os.chdir("/opt/scripts/series/output/")
try:
    os.stat("/opt/scripts/series/output/"+str(date_day)+"/")
except:
    os.mkdir("/opt/scripts/series/output/"+str(date_day)+"/")
os.chdir("/opt/scripts/series/output/"+str(date_day)+"/")
#Get number of pages
nb_pages = 0
hdr = {'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11',
       'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
       'Accept-Charset': 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
       'Accept-Encoding': 'none',
       'Accept-Language': 'en-US,en;q=0.8',
       'Connection': 'keep-alive'}
req = "http://www.dpstream.net/series"
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
#CSV FOR SERIES
csv_file = csv.writer(open("dps_series_db_"+str(date_day)+".csv", "wb"),delimiter=';')
csv_file.writerow(["ROW_ID","TITLE","ALT TITLE","RLS DATE","DURATION (MIN)","DIRECTORS","ACTORS","CATEGORIES","ORIGIN","NB_SEASONS","NB_EPISODES","DESCRIPTION","IMAGE","KEYWORDS"])
#CSV FOR SEASONS
csv_file_seasons = csv.writer(open("dps_series_db_seasons_"+str(date_day)+".csv", "a"),delimiter=';')
csv_file_seasons.writerow(["ROW_ID","SERIE_TITLE","SERIE_ROW_ID","NAME"])
#CSV FOR EPISODES
csv_file_episodes = csv.writer(open("dps_series_db_episodes_"+str(date_day)+".csv", "a"),delimiter=';')
csv_file_episodes.writerow(["ROW_ID","SERIE_TITLE","SERIE_ROW_ID","SEASON_NAME","SEASON_ROW_ID","NAME"])
#CSV FOR LINKS
csv_file_links = csv.writer(open("dps_series_db_links_"+str(date_day)+".csv", "a"),delimiter=';')
csv_file_links.writerow(["SERIE_TITLE","SERIE_ROW_ID","SEASON_NAME","SEASON_ROW_ID","EPISODE_NAME","EPISODE_ROW_ID","PLAYER","VERSION","QUALITY","LINK"])
serie_row_id=0
season_row_id =0
episode_row_id=0
for i in range(nb_pages):
    print "Parsing page : ",i+1,"/",nb_pages
    req = "http://www.dpstream.net/series-recherche?page="+str(i+1)
    try :
        request = urllib2.Request(req, headers=hdr)
        html = urllib2.urlopen(request).read()
    except :
        print 'REQUEST ERROR'
        print req
    soup = BeautifulSoup(html)
    full_tags = soup.findAll("h3", {"class" : "resultTitle uppercaseLetter pull-left"})
    for each_tag in full_tags:
        serie_row_id +=1
        row= {}
        ref_tag = each_tag.find_all("a")
        serie_page = ref_tag[0].get('href')
        req = "http://www.dpstream.net"+str(serie_page)
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
	    row['title'].replace(".","")		
            #print row['title']
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
            duration = tags[0].find_all("div")
            #DURATION
            duration = duration[0].find_all("b")
            row['duration'] = duration and duration[0].getText() or "ND"
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
            row['categories'] += categories and categories[0].getText() or "ND"
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
        #numberOfSeasons
        nb_seasons = soup.findAll("meta", {"itemprop" : "numberOfSeasons"})
        row['nb_seasons']= "ND"
        if nb_seasons:
            row['nb_seasons'] = nb_seasons[0]['content']
        #numberOfEpisodes
        nb_episodes = soup.findAll("meta", {"itemprop" : "numberOfEpisodes"})
        row['nb_episodes']= "ND"
        if nb_episodes:
            row['nb_episodes'] = nb_episodes[0]['content']
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
        #SEASONS
        seasons = soup.findAll("div", {"class" : "episodecontent-k"})
        for s in seasons:
            season_row_id+=1
            season_name = s.findAll("span", {"itemprop" : "name"})
            s_name = season_name and season_name[0].getText() or 'ND'
            csv_file_seasons.writerow([season_row_id,str(row['title']).strip(),serie_row_id,str(s_name).strip()])
            #EPISODES
            episodes = s.findAll("tr", {"class" : "item normalEpisode"})
            for ep in episodes:
                episode_row_id+=1
                episode_tag = ep.findAll("td", {"class" : "col-name"})
                if episode_tag:
                    href_tag = episode_tag[0].findAll("a")
                    episode_name = episode_tag[0].findAll("span",{"itemprop" : "name"})
                    episode_name = episode_name and episode_name[0].getText() or "ND"
                    csv_file_episodes.writerow([episode_row_id,str(row['title']).strip(),serie_row_id,str(s_name).strip(),season_row_id,episode_name.strip()])
                    #LINKS
                    if href_tag:
                        episode_page = href_tag[0].get('href')
                        req = "http://www.dpstream.net"+str(episode_page)
                        try :
                            request = urllib2.Request(req, headers=hdr)
                            html = urllib2.urlopen(request).read()
                        except :
                            print 'REQUEST ERROR'
                            print req
                        episode_soup = BeautifulSoup(html)
                        for number in ['0','1','2','3','4','5','6','7','8','9','10']:
                            tr_nodes = episode_soup.findAll("tr", {"id" : str(number)})
                            for tr in tr_nodes:
                                td_nodes = tr.find_all("td")
                                a_nodes = td_nodes[5].find_all("a")
                                link_row={
                                     'serie_title':row['title'],
                                     'serie_row_id':serie_row_id,
                                     'season_name':s_name,
                                     'season_row_id':season_row_id,
                                     'episode_name':episode_name,
                                     'episode_row_id':episode_row_id,
                                     'player':td_nodes and td_nodes[0].getText() or 'ND',
                                     'version':td_nodes and td_nodes[1].getText() or 'ND',
                                     'quality':td_nodes and td_nodes[2].getText() or 'ND',
                                     'link':a_nodes and a_nodes[0].get("href") or "ND",
                                     }
                                csv_file_links.writerow([str(link_row['serie_title']).strip(),link_row['serie_row_id'],
                                                   link_row['season_name'].strip(),link_row['season_row_id'],link_row['episode_name'].strip(),
                                                   link_row['episode_row_id'],str(link_row['player']).strip(),str(link_row['version']).strip(),
                                                   str(link_row['quality']).strip(),str(link_row['link']).strip()
                                                   ])

        csv_file.writerow([serie_row_id,str(row['title']).strip(),str(row['a_title']).strip(),str(row['rls_date']).strip(),
                           str(row['duration']).strip(),str(row['directors']).strip(),
                           str(row['actors']).strip(),str(row['categories']).strip(),str(row['countryoforigin']).strip(),
                           row['nb_seasons'],row['nb_episodes'],str(row['description']).strip(),
                           str(row['image']).strip(),row['keywords'].strip()])
