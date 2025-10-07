<?php

namespace DTOCompiler\helpers;

use const STDERR;
use const STDIN;
use const STDOUT;

/**
 * За основу взят Yii2 helper BaseConsole.php
 */
class Console
{
    // foreground color control codes
    const int FG_BLACK = 30;
    const int FG_RED = 31;
    const int FG_GREEN = 32;
    const int FG_YELLOW = 33;
    const int FG_BLUE = 34;
    const int FG_PURPLE = 35;
    const int FG_CYAN = 36;
    const int FG_GREY = 37;
    // background color control codes
    const int BG_BLACK = 40;
    const int BG_RED = 41;
    const int BG_GREEN = 42;
    const int BG_YELLOW = 43;
    const int BG_BLUE = 44;
    const int BG_PURPLE = 45;
    const int BG_CYAN = 46;
    const int BG_GREY = 47;
    // fonts style control codes
    const int RESET = 0;
    const int NORMAL = 0;
    const int BOLD = 1;
    const int ITALIC = 3;
    const int UNDERLINE = 4;
    const int BLINK = 5;
    const int NEGATIVE = 7;
    const int CONCEALED = 8;
    const int CROSSED_OUT = 9;
    const int FRAMED = 51;
    const int ENCIRCLED = 52;
    const int OVERLINED = 53;

    /**
     * Clears entire screen content by sending ANSI control code ED with argument 2 to the terminal.
     * Cursor position will not be changed.
     * **Note:** ANSI.SYS implementation used in windows will reset cursor position to upper left corner of the screen.
     */
    public static function clearScreen(): void
    {
        echo "\033[2J";
    }

    /**
     * Clears text from cursor to the beginning of the screen by sending ANSI control code ED with argument 1 to the terminal.
     * Cursor position will not be changed.
     */
    public static function clearScreenBeforeCursor(): void
    {
        echo "\033[1J";
    }

    /**
     * Clears text from cursor to the end of the screen by sending ANSI control code ED with argument 0 to the terminal.
     * Cursor position will not be changed.
     */
    public static function clearScreenAfterCursor(): void
    {
        echo "\033[0J";
    }

    /**
     * Clears the line, the cursor is currently on by sending ANSI control code EL with argument 2 to the terminal.
     * Cursor position will not be changed.
     */
    public static function clearLine(): void
    {
        echo "\033[2K";
    }

    /**
     * Clears text from cursor position to the beginning of the line by sending ANSI control code EL with argument 1 to the terminal.
     * Cursor position will not be changed.
     */
    public static function clearLineBeforeCursor(): void
    {
        echo "\033[1K";
    }

    /**
     * Clears text from cursor position to the end of the line by sending ANSI control code EL with argument 0 to the terminal.
     * Cursor position will not be changed.
     */
    public static function clearLineAfterCursor(): void
    {
        echo "\033[0K";
    }

    /**
     * Returns the ANSI format code.
     *
     * @param array $format An array containing formatting values.
     * You can pass any of the `FG_*`, `BG_*` and `TEXT_*` constants
     * and also [[xtermFgColor]] and [[xtermBgColor]] to specify a format.
     * @return string The ANSI format code according to the given formatting constants.
     */
    public static function ansiFormatCode(array $format): string
    {
        return "\033[" . implode(';', $format) . 'm';
    }

    /**
     * Will return a string formatted with the given ANSI style.
     *
     * @param string $string the string to be formatted
     * @param array $format An array containing formatting values.
     * You can pass any of the `FG_*`, `BG_*` and `TEXT_*` constants
     * and also [[xtermFgColor]] and [[xtermBgColor]] to specify a format.
     * @return string
     */
    public static function ansiFormat(string $string, array $format = []): string
    {
        $code = implode(';', $format);

        return "\033[0m" . ($code !== '' ? "\033[" . $code . 'm' : '') . $string . "\033[0m";
    }

    /**
     * Strips ANSI control codes from a string.
     *
     * @param string $string String to strip
     * @return string
     */
    public static function stripAnsiFormat(string $string): string
    {
        return preg_replace(self::ansiCodesPattern(), '', $string);
    }

    /**
     * Returns the length of the string without ANSI color codes.
     * @param string $string the string to measure
     * @return int the length of the string not counting ANSI format characters
     */
    public static function ansiStrlen(string $string): int
    {
        return mb_strlen(static::stripAnsiFormat($string));
    }

    private static function ansiCodesPattern(): string
    {
        return /** @lang PhpRegExp */ '/\033\[[\d;?]*\w/';
    }

    /**
     * Converts a string to ansi formatted by replacing patterns like %y (for yellow) with ansi control codes.
     *
     * Uses almost the same syntax as https://github.com/pear/Console_Color2/blob/master/Console/Color2.php
     * The conversion table is: ('bold' meaning 'light' on some
     * terminals). It's almost the same conversion table irssi uses.
     * <pre>
     *                  text      text            background
     *      ------------------------------------------------
     *      %k %K %0    black     dark grey       black
     *      %r %R %1    red       bold red        red
     *      %g %G %2    green     bold green      green
     *      %y %Y %3    yellow    bold yellow     yellow
     *      %b %B %4    blue      bold blue       blue
     *      %m %M %5    magenta   bold magenta    magenta
     *      %p %P       magenta (think: purple)
     *      %c %C %6    cyan      bold cyan       cyan
     *      %w %W %7    white     bold white      white
     *
     *      %F     Blinking, Flashing
     *      %U     Underline
     *      %8     Reverse
     *      %_,%9  Bold
     *
     *      %n     Resets the color
     *      %%     A single %
     * </pre>
     * First param is the string to convert, second is an optional flag if
     * colors should be used. It defaults to true, if set to false, the
     * color codes will just be removed (And %% will be transformed into %)
     *
     * @param string $string String to convert
     * @param bool $colored Should the string be colored?
     * @return string
     */
    public static function renderColoredString(string $string, bool $colored = true): string
    {
        static $conversions = [
            '%y' => [self::FG_YELLOW],
            '%g' => [self::FG_GREEN],
            '%b' => [self::FG_BLUE],
            '%r' => [self::FG_RED],
            '%p' => [self::FG_PURPLE],
            '%m' => [self::FG_PURPLE],
            '%c' => [self::FG_CYAN],
            '%w' => [self::FG_GREY],
            '%k' => [self::FG_BLACK],
            '%n' => [0], // reset
            '%Y' => [self::FG_YELLOW, self::BOLD],
            '%G' => [self::FG_GREEN, self::BOLD],
            '%B' => [self::FG_BLUE, self::BOLD],
            '%R' => [self::FG_RED, self::BOLD],
            '%P' => [self::FG_PURPLE, self::BOLD],
            '%M' => [self::FG_PURPLE, self::BOLD],
            '%C' => [self::FG_CYAN, self::BOLD],
            '%W' => [self::FG_GREY, self::BOLD],
            '%K' => [self::FG_BLACK, self::BOLD],
            '%N' => [0, self::BOLD],
            '%3' => [self::BG_YELLOW],
            '%2' => [self::BG_GREEN],
            '%4' => [self::BG_BLUE],
            '%1' => [self::BG_RED],
            '%5' => [self::BG_PURPLE],
            '%6' => [self::BG_CYAN],
            '%7' => [self::BG_GREY],
            '%0' => [self::BG_BLACK],
            '%F' => [self::BLINK],
            '%U' => [self::UNDERLINE],
            '%8' => [self::NEGATIVE],
            '%9' => [self::BOLD],
            '%_' => [self::BOLD],
        ];

        if ($colored) {
            $string = str_replace('%%', '% ', $string);
            foreach ($conversions as $key => $value) {
                $string = str_replace(
                    $key,
                    static::ansiFormatCode($value),
                    $string
                );
            }
            $string = str_replace('% ', '%', $string);
        } else {
            $string = preg_replace('/%((%)|.)/', '$2', $string);
        }

        return $string;
    }

    /**
     * Returns true if the stream supports colorization. ANSI colors are disabled if not supported by the stream.
     *
     * - windows without ansicon
     * - not tty consoles
     *
     * @param mixed $stream
     * @return bool true if the stream supports ANSI colors, otherwise false.
     */
    public static function streamSupportsAnsiColors(mixed $stream): bool
    {
        return DIRECTORY_SEPARATOR === '\\'
            ? getenv('ANSICON') !== false || getenv('ConEmuANSI') === 'ON'
            : function_exists('posix_isatty') && @posix_isatty($stream);
    }

    /**
     * Returns true if the console is running on windows.
     * @return bool
     */
    public static function isRunningOnWindows(): bool
    {
        return DIRECTORY_SEPARATOR === '\\';
    }

    /**
     * Returns terminal screen size.
     *
     * Usage:
     *
     * ```php
     * list($width, $height) = ConsoleHelper::getScreenSize();
     * ```
     *
     * @param bool $refresh whether to force checking and not re-use cached size value.
     * This is useful to detect changing window size while the application is running but may
     * not get up to date values on every terminal.
     * @return array|bool An array of ($width, $height) or false when it was not able to determine size.
     */
    public static function getScreenSize(bool $refresh = false): array|bool
    {
        static $size;
        if ($size !== null && !$refresh) {
            return $size;
        }

        if (static::isRunningOnWindows()) {
            $output = [];
            exec('mode con', $output);
            if (isset($output[1]) && str_contains($output[1], 'CON')) {
                return $size = [(int)preg_replace('~\D~', '', $output[4]), (int)preg_replace('~\D~', '', $output[3])];
            }
        } else {
            // try stty if available
            $stty = [];
            if (exec('stty -a 2>&1', $stty)) {
                $stty = implode(' ', $stty);

                // Linux stty output
                if (preg_match('/rows\s+(\d+);\s*columns\s+(\d+);/mi', $stty, $matches)) {
                    return $size = [(int)$matches[2], (int)$matches[1]];
                }

                // MacOS stty output
                if (preg_match('/(\d+)\s+rows;\s*(\d+)\s+columns;/mi', $stty, $matches)) {
                    return $size = [(int)$matches[2], (int)$matches[1]];
                }
            }

            // fallback to tput, which may not be updated on terminal resize
            if (($width = (int)exec('tput cols 2>&1')) > 0 && ($height = (int)exec('tput lines 2>&1')) > 0) {
                return $size = [$width, $height];
            }

            // fallback to ENV variables, which may not be updated on terminal resize
            if (($width = (int)getenv('COLUMNS')) > 0 && ($height = (int)getenv('LINES')) > 0) {
                return $size = [$width, $height];
            }
        }

        return $size = false;
    }

    /**
     * Gets input from STDIN and returns a string right-trimmed for EOLs.
     *
     * @param bool $raw If set to true, returns the raw string without trimming
     * @return string the string read from stdin
     */
    public static function stdin(bool $raw = false): string
    {
        return $raw ? fgets(STDIN) : rtrim(fgets(STDIN), PHP_EOL);
    }

    /**
     * Prints a string to STDOUT.
     *
     * @param string $string the string to print
     * @return int|bool Number of bytes printed or false on error
     */
    public static function stdout(string $string): bool|int
    {
        return fwrite(STDOUT, $string);
    }

    /**
     * Prints a string to STDERR.
     *
     * @param string $string the string to print
     * @return int|bool Number of bytes printed or false on error
     */
    public static function stderr(string $string): bool|int
    {
        return fwrite(STDERR, $string);
    }

    /**
     * Asks the user for input. Ends when the user types a carriage return (PHP_EOL). Optionally, It also provides a
     * prompt.
     *
     * @param string|null $prompt the prompt to display before waiting for input (optional)
     * @return string the user's input
     */
    public static function input(string $prompt = null): string
    {
        if (isset($prompt)) {
            static::stdout($prompt);
        }

        return static::stdin();
    }

    /**
     * Prints text to STDOUT appended with a carriage return (PHP_EOL).
     *
     * @param string|null $string the text to print
     * @return int|bool number of bytes printed or false on error.
     */
    public static function output(string $string = null): bool|int
    {
        return static::stdout($string . PHP_EOL);
    }

    /**
     * Prompts the user for input and validates it.
     *
     * @param string $text prompt string
     * @param array $options the options to validate the input:
     *
     * - `required`: whether it is required or not
     * - `default`: default value if no input is inserted by the user
     * - `pattern`: regular expression pattern to validate user input
     * - `validator`: a callable function to validate input. The function must accept two parameters:
     * - `input`: the user input to validate
     * - `error`: the error value passed by reference if validation failed.
     *
     * @return string the user input
     */
    public static function prompt(string $text, array $options = []): string
    {
        $options = array_merge(
            [
                'required' => false,
                'default' => null,
                'pattern' => null,
                'validator' => null,
                'error' => 'Invalid input.',
            ],
            $options
        );
        $error = null;

        top:
        $input = $options['default']
            ? static::input("$text [" . $options['default'] . '] ')
            : static::input("$text ");

        if ($input === '') {
            if (isset($options['default'])) {
                $input = $options['default'];
            } elseif ($options['required']) {
                static::output($options['error']);
                goto top;
            }
        } elseif ($options['pattern'] && !preg_match($options['pattern'], $input)) {
            static::output($options['error']);
            goto top;
        } elseif ($options['validator'] &&
            !call_user_func_array($options['validator'], [$input, &$error])
        ) {
            static::output($error ?? $options['error']);
            goto top;
        }

        return $input;
    }

    /**
     * Asks user to confirm by typing y or n.
     *
     * A typical usage looks like the following:
     *
     * ```php
     * if (Console::confirm("Are you sure?")) {
     *     echo "user typed yes\n";
     * } else {
     *     echo "user typed no\n";
     * }
     * ```
     *
     * @param string $message to print out before waiting for user input
     * @param bool $default this value is returned if no selection is made.
     * @return bool whether user confirmed
     */
    public static function confirm(string $message, bool $default = false): bool
    {
        while (true) {
            static::stdout($message . ' (yes|no) [' . ($default ? 'yes' : 'no') . ']:');
            $input = trim(static::stdin());

            if (empty($input)) {
                return $default;
            }

            if (!strcasecmp($input, 'y') || !strcasecmp($input, 'yes')) {
                return true;
            }

            if (!strcasecmp($input, 'n') || !strcasecmp($input, 'no')) {
                return false;
            }
        }
    }

    /**
     * Gives the user an option to choose from. Giving '?' as an input will show
     * a list of options to choose from and their explanations.
     *
     * @param string $prompt the prompt message
     * @param array $options Key-value array of options to choose from. Key is what is inputed and used, value is
     * what's displayed to end user by help command.
     *
     * @return string An option character the user chose
     */
    public static function select(string $prompt, array $options = []): string
    {
        top:
        static::stdout("$prompt [" . implode(',', array_keys($options)) . ',?]: ');
        $input = static::stdin();
        if ($input === '?') {
            foreach ($options as $key => $value) {
                static::output(" $key - $value");
            }
            static::output(' ? - Show help');
            goto top;
        } elseif (!array_key_exists($input, $options)) {
            goto top;
        }

        return $input;
    }

    private static ?int $_progressStart;
    private static bool|int|null $_progressWidth;
    private static string $_progressPrefix;
    private static ?int $_progressEta;
    private static int $_progressEtaLastDone = 0;
    private static ?int $_progressEtaLastUpdate;

    /**
     * Starts display of a progress bar on screen.
     *
     * This bar will be updated by [[updateProgress()]] and may be ended by [[endProgress()]].
     *
     * The following example shows a simple usage of a progress bar:
     *
     * ```php
     * Console::startProgress(0, 1000);
     * for ($n = 1; $n <= 1000; $n++) {
     *     usleep(1000);
     *     Console::updateProgress($n, 1000);
     * }
     * Console::endProgress();
     * ```
     *
     * Git clone like progress (showing only status information):
     *
     * ```php
     * Console::startProgress(0, 1000, 'Counting objects: ', false);
     * for ($n = 1; $n <= 1000; $n++) {
     *     usleep(1000);
     *     Console::updateProgress($n, 1000);
     * }
     * Console::endProgress("done." . PHP_EOL);
     * ```
     *
     * @param int $done the number of items that are completed.
     * @param int $total the total value of items that are to be done.
     * @param string $prefix an optional string to display before the progress bar.
     * Default to empty string which results in no prefix to be displayed.
     * @param bool|int|null $width optional width of the progressbar. This can be an integer representing
     * the number of characters to display for the progress bar or a float between 0 and 1 representing the
     * percentage of screen with the progress bar may take. It can also be set to false to disable the
     * bar and only show progress information like percent, number of items and ETA.
     * If not set, the bar will be as wide as the screen. Screen size will be detected using [[getScreenSize()]].
     * @see startProgress
     * @see updateProgress
     * @see endProgress
     */
    public static function startProgress(int $done, int $total, string $prefix = '', bool|int $width = null): void
    {
        self::$_progressStart = time();
        self::$_progressWidth = $width;
        self::$_progressPrefix = $prefix;
        self::$_progressEta = null;
        self::$_progressEtaLastDone = 0;
        self::$_progressEtaLastUpdate = time();

        static::updateProgress($done, $total);
    }

    /**
     * Updates a progress bar that has been started by [[startProgress()]].
     *
     * @param int $done the number of items that are completed.
     * @param int $total the total value of items that are to be done.
     * @param string|null $prefix an optional string to display before the progress bar.
     * Defaults to null meaning the prefix specified by [[startProgress()]] will be used.
     * If prefix is specified it will update the prefix that will be used by later calls.
     * @see startProgress
     * @see endProgress
     */
    public static function updateProgress(int $done, int $total, string $prefix = null): void
    {
        if ($prefix === null) {
            $prefix = self::$_progressPrefix;
        } else {
            self::$_progressPrefix = $prefix;
        }
        $width = static::getProgressbarWidth($prefix);
        $percent = ($total == 0) ? 1 : $done / $total;
        $info = sprintf('%d%% (%d/%d)', $percent * 100, $done, $total);
        self::setETA($done, $total);
        $info .= self::$_progressEta === null ? ' ETA: n/a' : sprintf(' ETA: %d sec.', self::$_progressEta);

        // Number extra characters outputted. These are opening [, closing ], and space before info
        // Since Windows uses \r\n\ for line endings, there's one more in the case
        $extraChars = static::isRunningOnWindows() ? 4 : 3;
        $width -= $extraChars + static::ansiStrlen($info);
        // skipping progress bar on very small display or if forced to skip
        if ($width < 5) {
            static::stdout("\r$prefix$info   ");
        } else {
            if ($percent < 0) {
                $percent = 0;
            } elseif ($percent > 1) {
                $percent = 1;
            }
            $bar = floor($percent * $width);
            $status = str_repeat('=', $bar);
            if ($bar < $width) {
                $status .= '>';
                $status .= str_repeat(' ', $width - $bar - 1);
            }
            static::stdout("\r$prefix" . "[$status] $info");
        }
        flush();
    }

    /**
     * Return width of the progressbar
     * @param string $prefix an optional string to display before the progress bar.
     * @return int screen width
     * @see updateProgress
     * @since 2.0.14
     */
    private static function getProgressbarWidth(string $prefix): int
    {
        $width = self::$_progressWidth;

        if ($width === false) {
            return 0;
        }

        $screenSize = static::getScreenSize(true);
        if ($screenSize === false && $width < 1) {
            return 0;
        }

        if ($width === null) {
            $width = $screenSize[0];
        } elseif ($width > 0 && $width < 1) {
            $width = floor($screenSize[0] * $width);
        }

        $width -= static::ansiStrlen($prefix);

        return $width;
    }

    /**
     * Calculate $_progressEta, $_progressEtaLastUpdate and $_progressEtaLastDone
     * @param int $done the number of items that are completed.
     * @param int $total the total value of items that are to be done.
     * @see updateProgress
     * @since 2.0.14
     */
    private static function setETA(int $done, int $total): void
    {
        if ($done > $total || $done == 0) {
            self::$_progressEta = null;
            self::$_progressEtaLastUpdate = time();
            return;
        }

        if ($done < $total && (time() - self::$_progressEtaLastUpdate > 1 && $done > self::$_progressEtaLastDone)) {
            $rate = (time() - (self::$_progressEtaLastUpdate ?: self::$_progressStart)) / ($done - self::$_progressEtaLastDone);
            self::$_progressEta = $rate * ($total - $done);
            self::$_progressEtaLastUpdate = time();
            self::$_progressEtaLastDone = $done;
        }
    }

    /**
     * Ends a progress bar that has been started by [[startProgress()]].
     *
     * @param bool|string $remove This can be `false` to leave the progress bar on screen and just print a newline.
     * If set to `true`, the line of the progress bar will be cleared. This may also be a string to be displayed instead
     * of the progress bar.
     * @param bool $keepPrefix whether to keep the prefix that has been specified for the progressbar when progressbar
     * gets removed. Defaults to true.
     * @see startProgress
     * @see updateProgress
     */
    public static function endProgress(bool|string $remove = false, bool $keepPrefix = true): void
    {
        if ($remove === false) {
            static::stdout(PHP_EOL);
        } else {
            if (static::streamSupportsAnsiColors(STDOUT)) {
                static::clearLine();
            }
            static::stdout("\r" . ($keepPrefix ? self::$_progressPrefix : '') . (is_string($remove) ? $remove : ''));
        }
        flush();

        self::$_progressStart = null;
        self::$_progressWidth = null;
        self::$_progressPrefix = '';
        self::$_progressEta = null;
        self::$_progressEtaLastDone = 0;
        self::$_progressEtaLastUpdate = null;
    }

    public static function info(string $message, bool $newLine = true): void
    {
        static::stdout(self::ansiFormat($message, [self::FG_CYAN]) . ($newLine ? PHP_EOL: ''));
    }

    public static function warning(string $message, bool $newLine = true): void
    {
        static::stdout(self::ansiFormat($message, [self::FG_YELLOW]) . ($newLine ? PHP_EOL: ''));
    }

    public static function success(string $message, bool $newLine = true): void
    {
        static::stdout(self::ansiFormat($message, [self::FG_GREEN]) . ($newLine ? PHP_EOL: ''));
    }

    public static function error(string $message, bool $newLine = true): void
    {
        static::stderr(self::ansiFormat($message, [self::FG_RED]) . ($newLine ? PHP_EOL: ''));
    }
}
