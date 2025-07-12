<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>界面示例1</title>
    <script src="https://js.zohostatic.com/creator/widgets/version/2.0/widgetsdk-min.js"></script>
    <!-- 引入 Vis.js 库 -->
    <script type="text/javascript" src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
    <style>
        /* 基本样式和布局 */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f7fafc;
            margin: 0;

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
            <button class="main-nav-btn">战况与风险111</button>
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
                <select id="themeSelect">
                    <option value="">核心目标</option>
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
        <!-- 网络图区域 -->
        <div id="networkGraphContainer" style="width: 100%; height: 500px; border: 1px solid #ccc; margin-top: 2rem; display: none;">
            <!-- 网络图将在此处渲染 -->
        </div>
    </div>

    <script>
        // 全局函数定义
        function updateNextButtonHref() {
            const selectedValue = nodeTypeSelect.value;
            let newHref = "https://creatorapp.zoho.com.cn/zoho_f.pwj/-demo#Form:";
            switch (selectedValue) {
                case "purpose":
                    newHref += "Goals"; // 假设目的对应 form2
                    break;
                case "plan":
                    newHref += "Plans"; // 假设计划对应 form3
                    break;
                case "plan_node":
                    newHref += "Plan_Nodes"; // 假设计划节点对应 form4
                    break;
                default:
                    newHref += "Goals"; // 默认值
            }
            newHref += "?zc_LoadIn=dialog";
            nextButton.href = newHref;
        }


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

        // Zoho Creator 数据获取和填充下拉菜单
        window.addEventListener('load', async function() {
            try {


                const config = {
                    app_name: '-demo', // 替换为你的应用名称
                    report_name: 'Quarterly_Fighting_Topics_Report', // 替换为你的报表名称
                };
                const response = await ZOHO.CREATOR.DATA.getRecords(config);
                // console.log('Zoho Creator Data:', response);

                // --- 配置区 ---
                const app_name = '-demo'; // 替换为你的应用名称

                // 定义所有需要获取数据的报表名称
                const report_names = [
                    'Goals_Report',
                    'Plans_Report',
                    'Plan_Nodes_Report'
                ];

                // --- 执行区 ---
                async function fetchAllData() {
                    console.log('开始并行获取所有报表数据...');

                    try {
                        // 1. 创建一个包含所有请求的 Promise 数组
                        // .map 会遍历 report_names 数组，并为每个报表名称返回一个 getRecords 的 Promise
                        const promises = report_names.map(report_name => {
                            const config = {
                                app_name: app_name,
                                report_name: report_name,
                            };
                            return ZOHO.CREATOR.DATA.getRecords(config);
                        });

                        // 2. 使用 Promise.all 来并行执行所有的 Promise
                        // 它会等待所有的请求都成功完成后，才继续执行
                        const results = await Promise.all(promises);
                        console.log('所有数据已成功获取!');

                        // 3. 将返回的结果组装到一个对象中，方便使用
                        const allData = {};
                        report_names.forEach((name, index) => {
                            // results 数组中的顺序与 report_names 中的顺序是一致的
                            // 我们将每个报表的数据存入 allData 对象，以报表名为键 (key)
                            allData[name] = results[index].data || [];
                        });

                        // 4. 打印最终组装好的数据对象
                        console.log('所有数据已组装完毕:', allData);

                        // 现在您可以通过 allData.Goals_Report, allData.Plans_Report 等方式来访问具体的数据了
                        // 例如:
                        // console.log('Goals 数据:', allData.Goals_Report);

                        return allData;

                    } catch (error) {
                        // 如果任何一个请求失败，Promise.all 就会立即抛出错误
                        console.error('在获取数据过程中发生错误:', error);
                    }
                }

                // 调用主函数来执行
                fetchAllData();

                if (response.code === 3000 && response.data) {
                    const themeSelect = document.getElementById('themeSelect');

                    const filteredThemes = response.data
                        .filter(item => item.status === '已完成' || item.status === '进行中')
                        .map(item => item.theme_name);

                    // 添加 themeSelect 的 change 事件监听器
                    themeSelect.addEventListener('change', function() {
                        if (this.value) {
                            networkGraphContainer.style.display = 'block'; // 显示网络图区域
                            initNetworkGraph(this.value); // 初始化网络图
                        } else {
                            networkGraphContainer.style.display = 'none'; // 隐藏网络图区域
                        }
                    });

                    // 初始化网络图函数
                    function initNetworkGraph(selectedTheme) {
                        const container = document.getElementById('networkGraphContainer');
                        const nodes = new vis.DataSet([]);
                        const edges = new vis.DataSet([]);

                        // 添加选定的主题作为节点
                        nodes.add({
                            id: selectedTheme,
                            label: selectedTheme,
                            color: '#6aa84f'
                        });

                        const data = {
                            nodes: nodes,
                            edges: edges
                        };

                        const options = {
                            nodes: {
                                shape: 'box',
                                size: 20,
                                font: {
                                    size: 14,
                                    color: '#ffffff'
                                },
                                borderWidth: 2,
                                shadow: true
                            },
                            edges: {
                                width: 2,
                                shadow: true
                            },
                            physics: {
                                enabled: true,
                                stabilization: {
                                    iterations: 2000
                                }
                            },
                            interaction: {
                                navigationButtons: true,
                                keyboard: true
                            }
                        };

                        const network = new vis.Network(container, data, options);

                        network.on("oncontext", function(params) {
                            params.event.preventDefault(); // 阻止默认的浏览器右键菜单
                            const nodeId = network.getNodeAt(params.pointer.DOM);
                            if (nodeId) {
                                // 如果点击的是节点，显示自定义菜单
                                const menu = document.createElement('div');
                                menu.style.position = 'absolute';
                                menu.style.top = `${params.event.clientY}px`;
                                menu.style.left = `${params.event.clientX}px`;
                                menu.style.backgroundColor = 'white';
                                menu.style.border = '1px solid #ccc';
                                menu.style.padding = '5px';
                                menu.style.zIndex = '1000';
                                menu.innerHTML = `
                                    <div style="padding-bottom: 5px;">
                                        <select id="nodeTypeSelect">
                                            <option value="purpose">目的</option>                               
                                            <option value="plan">计划</option>
                                            <option value="plan_node">计划节点</option>
                                        </select>
                                    </div>
                                    <button onclick="this.parentNode.remove();">
                                        <a id="nextButton" href="https://creatorapp.zoho.com.cn/zoho_f.pwj/-demo#Form:form2?zc_LoadIn=dialog" target="_top" style="display: block; padding: 5px; text-decoration: none; color: black;">下一步</a>
                                    </button>
                                `;
                                document.body.appendChild(menu);

                                // 获取动态创建的元素并添加事件监听器
                                const nodeTypeSelect = menu.querySelector('#nodeTypeSelect');
                                const nextButton = menu.querySelector('#nextButton');

                                // 初始设置 href
                                updateNextButtonHref();

                                // 添加事件监听器
                                nodeTypeSelect.addEventListener('change', updateNextButtonHref);

                                // 点击菜单外部时隐藏菜单
                                document.addEventListener('click', function hideMenu(event) {
                                    if (!menu.contains(event.target)) {
                                        menu.remove();
                                        document.removeEventListener('click', hideMenu);
                                    }
                                });
                            }
                        });
                    }

                    console.log('Filtered Themes:', filteredThemes);

                    // 移除重复项
                    const uniqueThemes = [...new Set(filteredThemes)];

                    uniqueThemes.forEach(theme => {
                        const option = document.createElement('option');
                        option.value = theme;
                        option.textContent = theme;
                        themeSelect.appendChild(option);
                    });
                } else {
                    console.error('从 Zoho Creator 获取数据失败:', response);
                }
            } catch (error) {
                console.error('初始化 Zoho Creator 或获取数据时出错:', error);
            }
        });
    </script>
</body>

</html>