<?php

/**
 * PubMed services
 */
class Application_Service_PubMed
{
    public static function getCitationData($pubmedId)
    {
        // see http://www.ncbi.nlm.nih.gov/entrez/query/DTD/pubmed_080101.dtd
        // for xml DTD definition
        $url = sprintf('http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?'
            . 'db=pubmed&report=citation&mode=xml&id=%d', intval($pubmedId));

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        $data = array();
        $xml = new SimpleXMLElement($response);
        if ($xml && $xml->PubmedArticle && $xml->PubmedArticle->MedlineCitation) {
            $article = $xml->PubmedArticle->MedlineCitation->Article;
        }
        if (isset($article)) {
            $title = (string)$article->ArticleTitle;
            $data['title'] = $title;

            // get source string
            $source = '';
            $pages = (string)$article->Pagination->MedlinePgn;
            if ($article->Journal) {
                $journal = $article->Journal;
                $source = '';
                $name = (string)$journal->ISOAbbreviation;
                if (strlen($name) > 0) {
                    $source .= $name . '.';
                }
                $time = (string)$journal->JournalIssue->PubDate->Year
                    . ' ' . (string)$journal->JournalIssue->PubDate->Month;
                if (strlen($time) > 0) {
                    $source .= ' ' . $time . ';';
                }
                $vol = (string)$journal->JournalIssue->Volume;
                if (strlen($vol) > 0) {
                    $source .= ' ' . $vol;
                }
                $issue = (string)$journal->JournalIssue->Issue;
                if (strlen($issue) > 0) {
                    $source .= '(' . $issue . ')';
                }
                if (strlen($pages) > 0) {
                    $source .= ':' . $pages;
                }
            }
            $data['source'] = $source;

            // get authors string
            $authorsArr = array();
            if ($article->AuthorList) {
                foreach ($article->AuthorList->xpath('//Author') as $author) {
                    $authorsArr[] = $author->LastName . " " . $author->Initials;
                }
            }
            $authors = join(", ", $authorsArr);
            $data['authors'] = $authors;

            // get abstract
            $abstract = (string)$article->Abstract->AbstractText;
            $data['abstract'] = $abstract;
        }

        $pubmedPubDate = $xml->PubmedArticle->PubmedData->History->PubMedPubDate;
        if ($pubmedPubDate) {
            $publishDate = sprintf("%04d-%02d-%02d",
                (string)$pubmedPubDate->Year, (string)$pubmedPubDate->Month, (string)$pubmedPubDate->Day);
            $data['publishDate'] = $publishDate;
        }

        if (count($data) > 0) {
            $data['url'] = 'http://www.ncbi.nlm.nih.gov/pubmed/' . $pubmedId;
        }

        return $data;
    }
}


