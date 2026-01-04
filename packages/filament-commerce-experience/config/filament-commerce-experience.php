<?php

return [
    'tables' => [
        'reviews' => 'exp_reviews',
        'review_votes' => 'exp_review_votes',
        'questions' => 'exp_questions',
        'answers' => 'exp_answers',
        'csat_surveys' => 'exp_csat_surveys',
        'csat_responses' => 'exp_csat_responses',
        'nps_surveys' => 'exp_nps_surveys',
        'nps_responses' => 'exp_nps_responses',
        'buy_now_preferences' => 'exp_buy_now_preferences',
    ],
    'api' => [
        'rate_limit' => '60,1',
    ],
    'buy_now' => [
        'enabled' => true,
        'requires_2fa' => false,
    ],
    'notifications' => [
        'panel' => 'tenant',
        'csat_event' => 'experience.csat.sent',
    ],
];
