<?php

namespace Opcodes\LogViewer\Logs;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Opcodes\LogViewer\LogLevels\NginxStatusLevel;

class HttpNginxErrorLog extends BaseLog
{
    public static string $regex = '/^(?P<datetime>[\d+\/ :]+) \[(?P<level>.+)\] .*?: (?P<errormessage>.+?)(?:, client: (?P<client>.+?))?(?:, server: (?P<server>.+?))?(?:, request: "?(?P<request>.+?)"?)?(?:, host: "?(?P<host>.+?)"?)?$/';

    public static string $levelClass = NginxStatusLevel::class;

    protected function fillMatches(array $matches = []): void
    {
        $this->datetime = static::parseDateTime($matches['datetime'] ?? null)?->tz(
            config('log-viewer.timezone', config('app.timezone', 'UTC'))
        );
        $this->level = $matches['level'] ?? null;
        $this->message = $matches['errormessage'] ?? null;

        $this->context = [
            'client' => $matches['client'] ?? null,
            'server' => $matches['server'] ?? null,
            'request' => $matches['request'] ?? null,
            'host' => $matches['host'] ?? null,
        ];
    }

    public static function parseDateTime(?string $datetime): ?CarbonInterface
    {
        return $datetime ? Carbon::createFromFormat('Y/m/d H:i:s', $datetime) : null;
    }
}
