<?php

namespace App\Services;

class WishMessageService
{
    public function generate(string $senderName, string $receiverName, string $relationship, string $style): string
    {
        $message = match ($style) {
            'religious' => "كل عام وأنت بخير يا {$receiverName}، تقبل الله منا ومنكم صالح الأعمال، وجعل عيد الأضحى المبارك فرحة وبركة عليك وعلى أهلك. مع أطيب التهاني من {$senderName}.",
            'funny' => "يا {$receiverName}، عيد أضحى مبارك عليك! ربنا يجعل أيامك كلها فرحة وضحك ولحمة مشوية. كل سنة وأنت طيب. من {$senderName}.",
            'romantic' => "كل عيد وأنت أجمل فرحة في حياتي يا {$receiverName}. عيد أضحى مبارك عليك، وربنا يديم وجودك نعمة في حياتي. من {$senderName}.",
            'corporate' => "يسر {$senderName} أن يهنئ {$receiverName} بحلول عيد الأضحى المبارك، متمنين لكم دوام النجاح والتوفيق، وكل عام وأنتم بخير.",
            default => "إلى {$receiverName}، بمناسبة عيد الأضحى المبارك، أتمنى لك أيامًا مليئة بالفرح والسكينة والبركة. كل عام وأنت بخير. من {$senderName}.",
        };

        return match ($relationship) {
            'mother' => $this->append($message, "يا أغلى أم، دعوتي أن يبقى قلبك مطمئنًا وأن تعودي علينا كل عيد بابتسامتك الحنونة."),
            'father' => $this->append($message, "لك كل الاحترام والتقدير يا سند البيت، وأسأل الله أن يبارك في عمرك وصحتك."),
            'wife', 'husband', 'fiancee' => $this->append($message, "وجودك يجعل العيد أجمل، وقربك هو الهدية التي لا تشبه شيئًا آخر."),
            'friend' => $this->append($message, "يا صاحب الأيام الحلوة، عساها أيام فرح وضحك ولمة لا تنتهي."),
            'customer' => $this->append($message, "نعتز بثقتكم، ونسأل الله أن يحمل لكم العيد مزيدًا من الخير والنجاح."),
            'manager', 'team' => $this->append($message, "نقدّر جهودكم وعطاءكم، ونتمنى لكم عيدًا سعيدًا وعودة موفقة بإذن الله."),
            default => $message,
        };
    }

    private function append(string $message, string $addition): string
    {
        return $message.' '.$addition;
    }
}
