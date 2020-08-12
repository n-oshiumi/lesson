<?php

const QUIZ = [
    'quiz1' => [
        'quiz' => 'Q1. ほとんどのプログラミング言語の入門書において最初の例題として掲載されている、最初に表示する文字は「Hello, ◯◯◯◯◯」。◯に当てはまる5文字を答えてください',
        'correct-answer' => 'World',
    ],
    'quiz2' => [
        'quiz' => 'Q2. PHPの略称として存在するものはどれか？',
        'check-content' => [
            [
                'id' => 'answer2-1',
                'choice' => 'PHP: Hypertext Preprocessor',
            ],
            [
                'id' => 'answer2-2',
                'choice' => 'Peace and Happiness through Prosperity',
            ],
            [
                'id' => 'answer2-3',
                'choice' => 'Pain Hyper Pain',
            ],
        ],
        'correct-answer' => ['PHP: Hypertext Preprocessor', 'Peace and Happiness through Prosperity'],
    ],
    'quiz3' => [
        'quiz' => 'Q3. PHPの生みの親は？',
        'check-content' => [
            [
                'id' => 'answer3-1',
                'choice' => 'ブレンダン・アイク氏',
            ],
            [
                'id' => 'answer3-2',
                'choice' => 'ラスマス・ラードフ氏',
            ],
            [
                'id' => 'answer3-3',
                'choice' => 'まつもと ゆきひろ氏',
            ],
        ],
        'correct-answer' => 'ラスマス・ラードフ氏',
    ],
];
