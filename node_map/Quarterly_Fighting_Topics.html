<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>界面示例</title>
    <style>
        /* 基本样式和布局 */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f7fafc;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 56rem;
            /* 896px */
        }

        .container>*:not(:first-child) {
            margin-top: 2rem;
        }

        /* 主导航栏样式 */
        .main-nav {
            display: flex;
            justify-content: flex-start;
            border-bottom: 1px solid #e5e7eb;
        }

        .main-nav-btn {
            padding: 10px 24px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            background-color: #f3f4f6;
            color: #4b5563;
            border-radius: 6px 6px 0 0;
            border: 1px solid transparent;
            position: relative;
        }

        .main-nav-btn.active {
            background-color: #ffffff;
            color: #111827;
            border-color: #e5e7eb;
            border-bottom-color: #ffffff;
            margin-bottom: -1px;
        }

        .main-nav-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #3b82f6;
        }

        /* 子导航栏样式 */
        .sub-nav {
            display: flex;
            align-items: center;
        }

        .sub-nav-btn {
            padding: 6px 16px;
            border: 1px solid #d1d5db;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s ease-in-out;
            background-color: #ffffff;
            color: #374151;
        }

        .sub-nav-btn.active {
            background-color: #4b5563;
            color: #ffffff;
            border-color: #4b5563;
        }

        .sub-nav-btn:first-of-type {
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
        }

        .sub-nav-btn:last-of-type {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
            border-left-width: 0;
        }

        /* 筛选器区域样式 */
        .filters {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #374151;
        }

        .filters>*:not(:first-child) {
            margin-left: 1.5rem;
            /* 24px */
        }

        .filters select {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
        }

        .filters select:focus {
            outline: none;
            box-shadow: 0 0 0 2px #3b82f6;
        }

        .radio-group {
            display: flex;
            align-items: center;
        }

        .radio-group>*:not(:first-child) {
            margin-left: 1rem;
            /* 16px */
        }

        .radio-label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .radio-label input {
            height: 1rem;
            width: 1rem;
            color: #2563eb;
        }

        .radio-label span {
            margin-left: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- 主导航栏 -->
        <div id="mainNav" class="main-nav">
            <button class="main-nav-btn">战况与风险</button>
            <button class="main-nav-btn">执行与任务</button>
            <button class="main-nav-btn active">协同与管理</button>
        </div>

        <div>
            <!-- 子导航栏 -->
            <div id="subNav" class="sub-nav">
                <button class="sub-nav-btn">作战主题计划</button>
                <button class="sub-nav-btn active">核心目标计划</button>
            </div>
        </div>

        <!-- 筛选器：下拉菜单和单选按钮 -->
        <div class="filters">
            <!-- 下拉菜单 -->
            <div>
                <select>
                    <option>核心目标</option>
                    <option>目标 A</option>
                    <option>目标 B</option>
                </select>
            </div>

            <!-- 单选按钮 -->
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="viewType" checked>
                    <span>季目标视图</span>
                </label>
                <label class="radio-label">
                    <input type="radio" name="viewType">
                    <span>月目标视图</span>
                </label>
            </div>
        </div>
    </div>

    <script>
        // 用于处理导航按钮活动状态的 JavaScript
        const mainNav = document.getElementById('mainNav');
        const subNav = document.getElementById('subNav');

        /**
         * 处理导航容器上的点击事件
         * @param {Event} e - 点击事件
         * @param {string} buttonSelector - 容器内按钮的 CSS 选择器
         */
        function handleNavClick(e, buttonSelector) {
            const clickedButton = e.target.closest(buttonSelector);
            if (!clickedButton) return; // 如果点击的不是按钮，则退出

            // 获取同一组中的所有按钮
            const buttons = clickedButton.parentElement.querySelectorAll(buttonSelector);

            // 从组中的所有按钮中移除 'active' 类
            buttons.forEach(btn => btn.classList.remove('active'));

            // 将 'active' 类添加到被点击的按钮上
            clickedButton.classList.add('active');
        }

        // 为导航容器添加事件监听器
        mainNav.addEventListener('click', (e) => handleNavClick(e, '.main-nav-btn'));
        subNav.addEventListener('click', (e) => handleNavClick(e, '.sub-nav-btn'));
    </script>
</body>

</html>