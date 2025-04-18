<?php
namespace App;

use Dompdf\Dompdf;
use Dompdf\Options;

class LicenseGenerator {
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function generate(): string {
        $html = $this->generateHtml();
        $filename = sprintf('license_%s_%s.html', $this->data['author_name'], date('Ymd_His'));
        $filepath = __DIR__ . '/../public/html/' . $filename;
        file_put_contents($filepath, $html);

        return $filename;
    }

    private function generateHtml(): string {
        $licenseType = $this->getLicenseType();
        $date = date('Y年m月d日');

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: "Microsoft YaHei", sans-serif; line-height: 1.6; }
                .header { text-align: center; margin-bottom: 30px; }
                .content { margin: 20px 0; }
                .footer { margin-top: 50px; }
                .signature { margin-top: 100px; text-align: right; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>知识共享协议许可授权书</h1>
            </div>
            <div class="content">
                <p>本授权书由以下双方签署：</p>
                <p><strong>著作权人：</strong>{$this->data['author_name']}</p>
                <p><strong>作者标识：</strong>{$this->data['author_id']}</p>
                <p><strong>作品标识：</strong>{$this->data['work_id']}</p>
                <p><strong>被授权人：</strong>新v学员</p>
                
                <p>根据本授权书，著作权人同意按照以下条款授权被授权人使用上述作品：</p>
                
                <h3>一、授权范围</h3>
                {$licenseType}
                
                <h3>二、授权期限</h3>
                <p>本授权自签署之日起生效，除非双方另有约定，本授权为永久性的。</p>
                
                <h3>三、其他约定</h3>
                <p><strong>联系方式：</strong>{$this->data['contact']}</p>
                <p><strong>附言：</strong>{$this->data['message']}</p>
            </div>
            
            <div class="signature">
                <p>著作权人：{$this->data['author_name']}</p>
                <p>日期：{$date}</p>
            </div>
        </body>
        </html>
        HTML;
    }

    private function getLicenseType(): string {
        $licenseText = '';
        switch($this->data['license_type']) {
            case 'cc_by':
                $licenseText = '本作品采用<strong>知识共享署名 4.0 国际许可协议</strong>进行许可。使用者需要注明原作者姓名，但可以自由地：共享、复制、发行本作品，创作演绎作品，以及进行商业性使用。';
                break;
            case 'cc_by_sa':
                $licenseText = '本作品采用<strong>知识共享署名-相同方式共享 4.0 国际许可协议</strong>进行许可。使用者需要注明原作者姓名，并且必须以相同的许可协议共享其演绎作品。';
                break;
            case 'cc_by_nc':
                $licenseText = '本作品采用<strong>知识共享署名-非商业性使用 4.0 国际许可协议</strong>进行许可。使用者需要注明原作者姓名，且不得进行商业性使用。';
                break;
            case 'custom':
                $options = $this->data['custom_options'] ?? [];
                $permissions = [];
                if (in_array('commercial', $options)) $permissions[] = '商业性使用';
                if (in_array('modify', $options)) $permissions[] = '修改和演绎';
                if (in_array('share', $options)) $permissions[] = '共享和分发';
                $licenseText = '本作品采用<strong>自定义授权协议</strong>进行许可。被授权人在注明原作者姓名的前提下，被允许进行以下使用：' . implode('、', $permissions) . '。';
                break;
        }
        return $licenseText;
    }
}