<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CC许可证生成器</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4">CC知识共享协议许可授权生成</h1>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form id="licenseForm" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="author_name" class="form-label">著作人姓名(网名)</label>
                        <input type="text" class="form-control" id="author_name" name="author_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="author_id" class="form-label">作者标识(作品发布平台的账号主页url或个人ID)</label>
                        <input type="text" class="form-control" id="author_id" name="author_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="work_id" class="form-label">作品标识(作品发布平台的作品主页url或作品ID)</label>
                        <input type="text" class="form-control" id="work_id" name="work_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">许可证选择</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="license_type" id="cc_by" value="cc_by" checked>
                            <label class="form-check-label" for="cc_by">CC BY (署名)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="license_type" id="cc_by_sa" value="cc_by_sa">
                            <label class="form-check-label" for="cc_by_sa">CC BY-SA (署名-相同方式共享)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="license_type" id="cc_by_nc" value="cc_by_nc">
                            <label class="form-check-label" for="cc_by_nc">CC BY-NC (署名-非商业性使用)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="license_type" id="custom" value="custom">
                            <label class="form-check-label" for="custom">自定义授权范围</label>
                        </div>
                    </div>
                    <div class="mb-3" id="customLicenseFields" style="display: none;">
                        <label class="form-label">自定义授权范围</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="custom_options[]" value="commercial">
                            <label class="form-check-label">允许商业使用</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="custom_options[]" value="modify">
                            <label class="form-check-label">允许修改</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="custom_options[]" value="share">
                            <label class="form-check-label">允许分享</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">即时或长期联系方式</label>
                        <input type="text" class="form-control" id="contact" name="contact" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">给被授权人的话</label>
                        <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">生成授权文件</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('licenseForm');
            const customFields = document.getElementById('customLicenseFields');
            const licenseTypeInputs = document.querySelectorAll('input[name="license_type"]');

            licenseTypeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    customFields.style.display = this.value === 'custom' ? 'block' : 'none';
                });
            });

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }

                const formData = new FormData(form);
                try {
                    const response = await fetch('/generate', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert('授权文件生成成功！');
                    } else {
                        alert('生成失败：' + (result.error || '未知错误'));
                    }
                } catch (error) {
                    alert('发生错误：' + error.message);
                }
            });
        });
    </script>
</body>
</html>