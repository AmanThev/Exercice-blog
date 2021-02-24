<?php

namespace App\Url;


class ExplodeUrl {

        private $path;

        public function __construct($path)
        {
            $this->path = $path;
        }

        public static function explodePath($path): array
        {
            $rePath = str_replace('/', '-', $path);
            $exPath = explode('-', $rePath);
            return $exPath;
        }

        public function getId(): int
        {
            $id = SELF::explodePath($this->path);
            return $id[2];
        }

        public function getIdForSubCat(): int
        {
            $id = SELF::explodePath($this->path);
            return $id[3];
        }

        public function getIdForTopic(): int
        {
            $id = SELF::explodePath($this->path);
            return $id[4];
        }

        public function getSlug(): string
        {
            $slug = SELF::explodePath($this->path);
            $slug = preg_split('/(?=[A-Z])/', $slug[1], -1, PREG_SPLIT_NO_EMPTY);
            $slug = $this->replaceApostrophe($slug);
            $slug = $this->getCountSlug($slug);
            return $slug;
        }

        public function getSlugName(): string
        {
            $slug = SELF::explodePath($this->path);
            $slug = preg_split('/(?=[A-Z])/', $slug[2], -1, PREG_SPLIT_NO_EMPTY);
            $slug = $this->getCountSlug($slug);

            return $slug;
        }

        public function getSlugSubCat(): string
        {
            $slug = SELF::explodePath($this->path);
            $slug = preg_split('/(?=[A-Z])/', $slug[2], -1, PREG_SPLIT_NO_EMPTY);
            $slug = $this->replaceApostrophe($slug);
            $slug = $this->getCountSlug($slug);
            return $slug;
        }
        
        public function getSlugTopic(): string
        {
            $slug = SELF::explodePath($this->path);
            $slug = preg_split('/(?=[A-Z0-9])/', $slug[3], -1, PREG_SPLIT_NO_EMPTY);
            $slug = $this->replaceApostrophe($slug);
            $slug = $this->getCountSlug($slug);
            return $slug;
        }
        
        public function getCatForPathTopic(): string
        {
            $slug = SELF::explodePath($this->path);
            $slug = preg_split('/(?=[A-Z])/', $slug[1], -1, PREG_SPLIT_NO_EMPTY);
            $slug = $this->getCountSlug($slug);
            return $slug;
        }

        public function getSubCatForPathTopic(): string
        {
            $slug = SELF::explodePath($this->path);
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
