<?php
require_once("vendor/autoload.php");
use Symfony\Component\DomCrawler\Crawler as Crawler;
ini_set('max_execution_time', 0);

/**
 * A class containing the logic of site parsing https://saratov.metal100.ru
 * Class Parser
 */
class Parser
{
    private string $url = "https://saratov.metal100.ru";

    /**
     * @return array
     * Get all product categories
     */
    public function getCategories(): array
    {
        $html = file_get_contents($this->url . "/prodazha/Truboprovodnaya-armatura/");
        $crawler = new Crawler($html);

        return $crawler->filter('.Categories')->filter('.subCategories')->filter('li')->each(function ($node) {
            return [
                'href' => $this->url . $node->filter('a')->attr('href'),
            ];
        });
    }

    /**
     * @param string $url
     * @return array
     * Get all links to products in the category
     */
    public function getProductsLinks(string $url): array
    {
        $html = file_get_contents($url);
        $crawler = new Crawler($html);

        return $crawler->filter('#sizesList')->filter('div.margin-bottom-20.buttonSet')->filter('span')->each(function ($node) {
            return [
                'url' => $this->url . $node->attr('url'),
            ];
        });
    }

    /**
     * @param string $url
     * @return array
     * Get all the necessary information for each product
     */
    public function getInfo(string $url): array
    {
        $html = file_get_contents($url);
        $crawler = new Crawler($html);

        return $crawler->filter('#priceTable')->filter('tbody')->filter('tr.priceRow')->each(function ($node) {
            return [
                'name' => $node->filter('td:nth-child(1)')->text(),
                'price' => $node->filter('td:nth-child(6)')->filter('span.hidden')->text(),
                'company' => $node->filter('td.nowrap.companyCell ')->filter('a')->text(),
            ];
        });
    }

    /**
     * @return array
     * The logic of adding information to the database
     */
    public function getResultArray(): array
    {
        $categories = $this->getCategories();
        $products = [];
        $info = [];

        foreach ($categories as $category) {
            foreach ($this->getProductsLinks($category['href']) as $item) {
                $products[] = $item;
            }
        }

        foreach ($products as $product) {
            foreach ($this->getInfo($product['url']) as $item) {
                $info[] = $item;
            }
        }

        return $info;
    }
}
