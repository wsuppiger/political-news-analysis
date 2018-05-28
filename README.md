# political-news-analysis
The goal of this project was to gather data from news sources using IBM Watson's tone analyzer.

## Working example:
### https://projects.suppiger.org/projects/politicalNews.php

## functions.php
### URLtoArticle()

##### purpose
The URLtoArticle uses the [get_fcontent()](https://stackoverflow.com/a/5402193/7873106 "get_fcontent()") function to retrieve the articles code.  Next it checks if the article is from one of the four known news sources (nytimes.com, nbcnews.com, foxnews.com, or washingtonpost.com) so that it can parse the article out.  If it is not from one of these sources, it just pulls out the paragraph elements from the web page

##### parameters
1. $url - the url of the article
2. $tld - default is ".com", but this parameter takes any TLD

### URLtoSource()

##### purpose
Parses the source out of the URL for the URLtoArticle() function.

##### parameters
1. $url - the url of the article
2. $tld - default is ".com", but this parameter takes any TLD

### analyzeArticle()

##### purpose
This takes the article and uses [the watson sdk](https://github.com/CognitiveBuild/WatsonPHPSDK "the watson sdk") to send and recieve the article's analysis.  Make sure you change the global variables $usernameWatson and $passwordWatson for it to work correctly.  It then stores the article & tones in a SQL table for later analysis.

##### parameters
1. $article - string that is the article
2. $url - the url of the article
3. $SQLtable - table to store SQL results in

### mysqli_insert()

##### purpose
function found  [here](https://stackoverflow.com/a/13480009/7873106 "here") and it is used to convert an associative array to a sql statement.  Make sure to update SQL $username, $password, and $db variables found in the function in order to work.

##### parameters
1. $table - the table name to insert the data into
2. $assoc - the associative array of the data

##### SQL table columns:
1. news source
2. url
3. story
4. anger
5. disgust
6. fear
7. joy
8. sadness
9. analytical
10. confident
11. tentative
12. openness_big5
13. conscientiousness_big5
14. extraversion_big5
15. agreeableness_big5
16. emotional_range_big5

### get_fcontent()
For more information [click here](https://stackoverflow.com/a/5402193/7873106 "get_fcontent()") 
