<?php

namespace App\Url;


class ExplodeUrl {

        private $path;

        public function __construct($path)
        {
            $this->path = $path;
        }

        public function explodePath(): array
        {
            $rePath = str_replace('/', '-', $this->path);
            $exPath = explode('-', $rePath);
            return $exPath;
        }

        public function getId(): int
        {
            $id = $this->explodePath();
            return $id[2];
        }

        public function getIdForSubCat(): int
        {
            $id = $this->explodePath();
            return $id[3];
        }

        public function getIdForTopic(): int
        {
            $id = $this->explodePath();
            return $id[4];
        }

        public function getSlug(): string
        {
            $slug = $this->explodePath();
            $slug = preg_split('/(?=[A-Z])/', $slug[1], -1, PREG_SPLIT_NO_EMPTY);
            $slug = $this->replaceApostrophe($slug);
            $slug = $this->getCountSlug($slug);
            return $slug;
        }

        public function getSlugSubCat(): string
        {
            $slug = $this->explodePath();
            $slug = preg_split('/(?=[A-Z])/', $slug[2], -1, PREG_SPLIT_NO_EMPTY);
            $slug = $this->replaceApostrophe($slug);
            $slug = $this->getCountSlug($slug);
            return $slug;
        }
        
        public function getSlugTopic(): string
        {
            $slug = $this->explodePath();
            $slug = preg_split('/(?=[A-Z0-9])/', $slug[3], -1, PREG_SPLIT_NO_EMPTY);
            $slug = $this->replaceApostrophe($slug);
            $slug = $this->getCountSlug($slug);
            return $slug;
        }
        
        public function getCatForPathTopic(): string
        {
            $slug = $this->explodePath();
            $slug = preg_split('/(?=[A-Z])/', $slug[1], -1, PREG_SPLIT_NO_EMPTY);
            $slug = $this->getCountSlug($slug);
            return $slug;
        }

        public function getSubCatForPathTopic(): string
        {
            $slug = $this->explodePath();
            $slug = preg_split('/(?=[A-Z])/', $slug[2], -1, PREG_SPLIT_NO_EMPTY);
            $slug = $this->getCountSlug($slug);
            return $slug;
        }
        
        /**
         * @param  string|array $slug
         * @return string
         */
        private function getCountSlug($slug): string
        {
            $countSlug = count($slug);
            if($countSlug > 1){
                $slug = implode(' ', $slug);
                return $slug;
            }
            return $slug[0];
        }
        
        /**
         * @param  string|array $slug
         * @return string|array
         */
        private function replaceApostrophe($slug)
        {
        $slug = preg_replace("#[_]+#", '\'', $slug);
        return $slug;
        }
}
