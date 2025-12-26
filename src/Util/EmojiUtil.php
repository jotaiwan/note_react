<?php

namespace Note\Util;

class EmojiUtil {

    public static function getEmojis()
    {
        $emojis = static::getEmojiIconMapper();

        $emojiList = [];
        foreach ($emojis as $label => $info) {
            $emojiList[] = [
                'label' => $label,
                'type' => $info['type'],
                'value' => $info['value']
            ];
        }

        return $emojiList;
    }

    public static function getEmojiIconMapper()
    {
        $code = "<strong>" . htmlspecialchars("</>", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</strong>";
        $redStar = "<span class='red-star-small'>★</span>";
        $redRoundX = "<span class='emoji-round-red-x'>x</span>";
        $strikeThrough = "<span class='strikethrough text-larger'>AU</span>";

        return array(
            // Custom markup replacements
            // $redStar => ["type" => "tag", "value" => $redRoundX],
            // $code => ["type" => "tag", "value" => "{code}\n\n{/code}"],
            // "💬" => ["type" => "entity", "value" => "{blockquote}\n\n{/blockquote}"],

            // $redRoundX => ["type" => "tag", "value" => $redRoundX],
            // $strikeThrough => ["type" => "tag", "value" => "{strikethrough}\n\n{/strikethrough}"],
            // HTML entities
            "👀" => ["type" => "entity", "value" => "👀"],
            "🔍" => ["type" => "entity", "value" => "🔍"],
            "💥" => ["type" => "entity", "value" => "💥"],
            "🔥" => ["type" => "entity", "value" => "🔥"],
            "⚠️" => ["type" => "entity", "value" => "⚠️"],
            "✅" => ["type" => "entity", "value" => "✅"],
            "🚨" => ["type" => "entity", "value" => "🚨"],
            "🛟" => ["type" => "entity", "value" => "🛟"],
            "📅" => ["type" => "entity", "value" => "📅"],
            "🧠" => ["type" => "entity", "value" => "🧠"],
            "🙋" => ["type" => "entity", "value" => "🙋"],
            "😁" => ["type" => "entity", "value" => "😁"],
            "😄" => ["type" => "entity", "value" => "😄"],
            "😂" => ["type" => "entity", "value" => "😂"],
            "🤔" => ["type" => "entity", "value" => "🤔"],
            "😮‍💨" => ["type" => "entity", "value" => "😮‍💨"],
            "😵" => ["type" => "entity", "value" => "😵"],
            "😵‍💫" => ["type" => "entity", "value" => "😵‍💫"],
            "😅" => ["type" => "entity", "value" => "😅"],
            "😥" => ["type" => "entity", "value" => "😥"],
            "😩" => ["type" => "entity", "value" => "😩"],
            "🤪" => ["type" => "entity", "value" => "🤪"],
            "😭" => ["type" => "entity", "value" => "😭"],
            "😤" => ["type" => "entity", "value" => "😤"],
            "😱" => ["type" => "entity", "value" => "😱"],
            "🤯" => ["type" => "entity", "value" => "🤯"],
            "🤨" => ["type" => "entity", "value" => "🤨"],
            "🥵" => ["type" => "entity", "value" => "🥵"],
            "🐞" => ["type" => "entity", "value" => "🐞"],
            "🔴" => ["type" => "entity", "value" => "🔴"],
            "🔺" => ["type" => "entity", "value" => "🔺"],
            "🔻" => ["type" => "entity", "value" => "🔻"],
            "📍" => ["type" => "entity", "value" => "📍"],
            "❗" => ["type" => "entity", "value" => "❗"],
            "❓" => ["type" => "entity", "value" => "❓"],
            "🟠" => ["type" => "entity", "value" => "🟠"],
            "🔸" => ["type" => "entity", "value" => "🔸"],
            "🟡" => ["type" => "entity", "value" => "🟡"],
            "🟢" => ["type" => "entity", "value" => "🟢"],
            "🔹" => ["type" => "entity", "value" => "🔹"],
            "💪" => ["type" => "entity", "value" => "💪"],
            "👍" => ["type" => "entity", "value" => "👍"],
            "👉" => ["type" => "entity", "value" => "👉"],
            "👈" => ["type" => "entity", "value" => "👈"],
            "👇" => ["type" => "entity", "value" => "👇"],
            "👌" => ["type" => "entity", "value" => "👌"],
            "⛔️" => ["type" => "entity", "value" => "⛔️"],
            "🚫" => ["type" => "entity", "value" => "🚫"],
            "💡" => ["type" => "entity", "value" => "💡"],
            "📌" => ["type" => "entity", "value" => "📌"],
            "🧩" => ["type" => "entity", "value" => "🧩"],
            "🎉" => ["type" => "entity", "value" => "🎉"],
            "🙏" => ["type" => "entity", "value" => "🙏"],
            "🎯" => ["type" => "entity", "value" => "🎯"],
            "ℹ️" => ["type" => "entity", "value" => "ℹ️"],
            "🔼" => ["type" => "entity", "value" => "🔼"],
            "➡️" => ["type" => "entity", "value" => "➡️"],
            "⬅️" => ["type" => "entity", "value" => "⬅️"],
            "⬆️" => ["type" => "entity", "value" => "⬆️"],
            "⬇️" => ["type" => "entity", "value" => "⬇️"],
            "↔️" => ["type" => "entity", "value" => "↔️"],
            "↕️" => ["type" => "entity", "value" => "↕️"],
            "⤴️" => ["type" => "entity", "value" => "⤴️"],
            "⤵️" => ["type" => "entity", "value" => "⤵️"],
            "↩️" => ["type" => "entity", "value" => "↩️"],
            "↪️" => ["type" => "entity", "value" => "↪️"],
            "🔁" => ["type" => "entity", "value" => "🔁"],
            "🔄" => ["type" => "entity", "value" => "🔄"],
            "0️⃣" => ["type" => "entity", "value" => "0️⃣"],
            "1️⃣" => ["type" => "entity", "value" => "1️⃣"],
            "2️⃣" => ["type" => "entity", "value" => "2️⃣"],
            "3️⃣" => ["type" => "entity", "value" => "3️⃣"],
            "4️⃣" => ["type" => "entity", "value" => "4️⃣"],
            "5️⃣" => ["type" => "entity", "value" => "5️⃣"],
            "6️⃣" => ["type" => "entity", "value" => "6️⃣"],
            "7️⃣" => ["type" => "entity", "value" => "7️⃣"],
            "8️⃣" => ["type" => "entity", "value" => "8️⃣"],
            "9️⃣" => ["type" => "entity", "value" => "9️⃣"],
            "👥" => ["type" => "entity", "value" => "👥"],
            "🧑‍🤝‍🧑" => ["type" => "entity", "value" => "🧑‍🤝‍🧑"],
            "👨‍👩‍👧‍👦" => ["type" => "entity", "value" => "👨‍👩‍👧‍👦"],
            "👤" => ["type" => "entity", "value" => "👤"],
            "🕰️" => ["type" => "entity", "value" => "🕰️"],
            "🏷️" => ["type" => "entity", "value" => "🏷️"],
            "🔖" => ["type" => "entity", "value" => "🔖"],
            "🧪" => ["type" => "entity", "value" => "🧪"],
            "📎" => ["type" => "entity", "value" => "📎"],
            "🧬" => ["type" => "entity", "value" => "🧬"],
            "⚗️" => ["type" => "entity", "value" => "⚗️"],
            "🔒" => ["type" => "entity", "value" => "🔒"],
            "🔓" => ["type" => "entity", "value" => "🔓"],
            "☑️" => ["type" => "entity", "value" => "☑️"],
            "⬜" => ["type" => "entity", "value" => "⬜"],
            "⚡" =>  ["type" => "entity", "value" => "⚡"],
            "💨" =>  ["type" => "entity", "value" => "💨"],
            "🔑" => ["type" => "entity", "value" => "🔑"],
            "🗝️" => ["type" => "entity", "value" => "🗝️"],
            "🛡️" => ["type" => "entity", "value" => "🛡️"],
            "🕒" => ["type" => "entity", "value" => "🕒"],
            "🌀" => ["type" => "entity", "value" => "🌀"],
            "⏰" => ["type" => "entity", "value" => "⏰"],
            "🔔" => ["type" => "entity", "value" => "🔔"],
            "❌" => ["type" => "entity", "value" => "❌"],
            "🚧" => ["type" => "entity", "value" => "🚧"],
            "👨‍💻" => ["type" => "entity", "value" => "👨‍💻"],
            "👷‍♂️" => ["type" => "entity", "value" => "👷‍♂️"],
            "🏭" => ["type" => "entity", "value" => "🏭"],
            "🏃‍♂️" => ["type" => "entity", "value" => "🏃‍♂️"],
            "🇦🇺" => ["type" => "entity", "value" => "🇦🇺"],
            "🐼" => ["type" => "entity", "value" => "🐼"],
            "🦘" => ["type" => "entity", "value" => "🦘"],
            "🐨" => ["type" => "entity", "value" => "🐨"],
            "🪲" => ["type" => "entity", "value" => "🪲"],
            "🐛" => ["type" => "entity", "value" => "🐛"],
            "🐜" => ["type" => "entity", "value" => "🐜"],
            "🌐" => ["type" => "entity", "value" => "🌐"],
            "🐚" => ["type" => "entity", "value" => "🐚"],

        );
    }
}