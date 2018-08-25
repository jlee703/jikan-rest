<?php

namespace App\Providers;

use Jikan\Model\Common\DateRange;
use Jikan\Model\Common\MalUrl;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class SerializerFactory
{

    public static function createV2(): Serializer
    {
        $serializer = (new SerializerBuilder())
            ->addMetadataDir(__DIR__.'/../../storage/app/metadata.v2')
            ->configureHandlers(
                function (HandlerRegistry $registry) {
                    $registry->registerHandler(
                        'serialization',
                        MalUrl::class,
                        'json',
                        \Closure::fromCallable('self::convertMalUrlv2')
                    );

                    $registry->registerHandler(
                        'serialization',
                        DateRange::class,
                        'json',
                        \Closure::fromCallable('self::convertDateRange')
                    );

                    $registry->registerHandler(
                        'serialization',
                        \DateTimeImmutable::class,
                        'json',
                        \Closure::fromCallable('self::convertDateTimeImmutable')
                    );
                }
            )
            ->build();
        $serializer->setSerializationContextFactory(new SerializationContextFactory());
        return $serializer;

    }

    public static function createV3(): Serializer
    {
        $serializer = (new SerializerBuilder())
            ->addMetadataDir(__DIR__.'/../../storage/app/metadata.v3')
            ->configureHandlers(
                function (HandlerRegistry $registry) {
                    $registry->registerHandler(
                        'serialization',
                        MalUrl::class,
                        'json',
                        \Closure::fromCallable('self::convertMalUrl')
                    );

                    $registry->registerHandler(
                        'serialization',
                        DateRange::class,
                        'json',
                        \Closure::fromCallable('self::convertDateRange')
                    );

                    $registry->registerHandler(
                        'serialization',
                        \DateTimeImmutable::class,
                        'json',
                        \Closure::fromCallable('self::convertDateTimeImmutable')
                    );
                }
            )
            ->build();
        $serializer->setSerializationContextFactory(new SerializationContextFactory());

        return $serializer;
    }

    private static function convertMalUrl($visitor, MalUrl $obj, array $type): array
    {
        return [
            'mal_id' => $obj->getMalId(),
            'type'   => $obj->getType(),
            'name'   => $obj->getTitle(),
            'url'    => $obj->getUrl(),
        ];
    }

    private static function convertMalUrlv2($visitor, MalUrl $obj, array $type): array
    {
        return [
            'mal_id' => $obj->getMalId(),
            'type'   => $obj->getType(),
            'title'   => $obj->getTitle(),
            'name'   => $obj->getTitle(),
            'url'    => $obj->getUrl(),
        ];
    }

    private static function convertDateRange($visitor, DateRange $obj, array $type): array
    {
        return [
            'from'   => $obj->getFrom() ? $obj->getFrom()->format(DATE_ATOM) : null,
            'to'     => $obj->getUntil() ? $obj->getUntil()->format(DATE_ATOM) : null,
            'string' => (string)$obj,
        ];
    }

    private static function convertDateTimeImmutable($visitor, \DateTimeImmutable $obj, array $type): ?string
    {
        return $obj ? $obj->format(DATE_ATOM) : null;
    }
}