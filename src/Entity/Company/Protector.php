<?php

namespace App\Entity\Company;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Company\ProtectorRepository")
 */
class Protector
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $correctOption;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $wrongOptionFirst;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $wrongOptionSecond;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $wrongOptionThird;

    public function getId()
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCorrectOption(): ?string
    {
        return $this->correctOption;
    }

    public function setCorrectOption(string $correctOption): self
    {
        $this->correctOption = $correctOption;

        return $this;
    }

    public function getWrongOptionFirst(): ?string
    {
        return $this->wrongOptionFirst;
    }

    public function setWrongOptionFirst(string $wrongOptionFirst): self
    {
        $this->wrongOptionFirst = $wrongOptionFirst;

        return $this;
    }

    public function getWrongOptionSecond(): ?string
    {
        return $this->wrongOptionSecond;
    }

    public function setWrongOptionSecond(string $wrongOptionSecond): self
    {
        $this->wrongOptionSecond = $wrongOptionSecond;

        return $this;
    }

    public function getWrongOptionThird(): ?string
    {
        return $this->wrongOptionThird;
    }

    public function setWrongOptionThird(string $wrongOptionThird): self
    {
        $this->wrongOptionThird = $wrongOptionThird;

        return $this;
    }

    public function getWrongOptions(): array
    {
        return [
            $this->wrongOptionFirst,
            $this->wrongOptionSecond,
            $this->wrongOptionThird,
        ];
    }

    public function getShuffleOptions(): array
    {
        $options = [
            0 => ['correct' => $this->correctOption],
            1 => ['wrong1' => $this->wrongOptionFirst],
            2 => ['wrong2' => $this->wrongOptionSecond],
            3 => ['wrong3' => $this->wrongOptionThird],
        ];

        shuffle($options);

        $shuffledOptions = [];

        foreach ($options as $arrayOption) {
            foreach ($arrayOption as $key => $option) {
                $shuffledOptions[$key] = $option;
            }
        }

        return $shuffledOptions;
    }
}
