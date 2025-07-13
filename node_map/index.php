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
        function updateNextButtonHref(nodeId = null) {
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
            
            // 添加基础参数
            newHref += "?zc_LoadIn=dialog";
            
            // 如果有 nodeId，添加 Father_Node_ID 参数
            if (nodeId !== null && nodeId !== undefined) {
                newHref += `&Father_Node_ID=${encodeURIComponent(nodeId)}`;
                console.log(`🔗 添加 Father_Node_ID 参数: ${nodeId}`);
            }
            
            nextButton.href = newHref;
            console.log(`🔗 更新后的链接: ${newHref}`);
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
                    'Plans_Report'

                ];

                // --- 执行区 ---
                async function fetchAllData() {
                    console.log('开始逐个获取报表数据...');
                    const allData = {};
                    // 逐个获取每个报表的数据
                    for (let i = 0; i < report_names.length; i++) {
                        const report_name = report_names[i];
                        console.log(`📊 正在获取报表: ${report_name} (${i + 1}/${report_names.length})`);
                        try {
                            const config = {
                                app_name: app_name,
                                report_name: report_name,
                            };
                            // 打印参数
                            console.log('获取报表数据的参数:', config);
                            const result = await ZOHO.CREATOR.DATA.getRecords(config);
                            // 报表数据存入对象之前先判断是否为空
                            let reportData = [];
                            if (result && typeof result === 'object') {
                                if (result.data && Array.isArray(result.data)) {
                                    reportData = result.data;
                                    console.log(`✅ 报表 ${report_name} 数据有效，包含 ${reportData.length} 条记录`);
                                } else if (result.data) {
                                    // 如果 data 存在但不是数组，尝试转换
                                    console.warn(`⚠️ 报表 ${report_name} 的数据不是数组格式，尝试转换:`, result.data);
                                    reportData = Array.isArray(result.data) ? result.data : [result.data];
                                } else {
                                    console.log(`ℹ️ 报表 ${report_name} 暂无数据`);
                                }
                            } else {
                                console.warn(`⚠️ 报表 ${report_name} 的结果对象无效:`, result);
                            }
                            // 存入数据对象
                            allData[report_name] = reportData;
                        } catch (error) {
                            console.log(`🔍 报表 ${report_name} 获取出错:`, error);
                            // 检查是否是"无记录"错误
                            let isNoRecordsError = false;
                            if (error.responseText) {
                                try {
                                    const errorData = JSON.parse(error.responseText);
                                    if (errorData.code === 9220) {
                                        isNoRecordsError = true;
                                    }
                                } catch (parseError) {
                                    // 解析失败，继续其他检查
                                }
                            }
                            if (isNoRecordsError) {
                                console.log(`📋 报表 ${report_name} 暂无记录，设置为空数组`);
                                allData[report_name] = [];
                            } else {
                                console.error(`❌ 报表 ${report_name} 获取失败，设置为空数组:`, error);
                                allData[report_name] = [];
                            }
                        }
                    }

                    // 自动生成 Joint_Report
                    const jointReport = [];
                    for (const reportName of report_names) {
                        const items = allData[reportName] || [];
                        for (const item of items) {
                            // 克隆对象，避免污染原数据
                            const newItem = Object.assign({}, item);
                            newItem.Node_Type = reportName.replace('_Report', '');
                            jointReport.push(newItem);
                        }
                    }
                    allData['Joint_Report'] = jointReport;
                    console.log('📦 所有报表数据获取完毕（含 Joint_Report ）:', allData);
                    return allData;
                }

                // 调用主函数来执行
                fetchAllData().then(function(allData) {
                    window.allData = allData;
                });

                if (response.code === 3000 && response.data) {
                    const themeSelect = document.getElementById('themeSelect');

                    // 报表数据处理前先判断是否为空
                    let filteredThemes = [];
                    if (response.data && Array.isArray(response.data)) {
                        filteredThemes = response.data
                            .filter(item => {
                                // 确保 item 存在且有 status 属性
                                return item && item.status && 
                                       (item.status === '已完成' || item.status === '进行中');
                            })
                            .map(item => {
                                // 确保 theme_name 存在且不为空
                                return item.theme_name || '未命名主题';
                            })
                            .filter(themeName => themeName && themeName.trim() !== ''); // 过滤空字符串
                        console.log(`主题数据处理完成，共 ${filteredThemes.length} 个有效主题`);
                    } else {
                        console.warn('响应数据不是有效的数组格式:', response.data);
                    }

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

                        // 获取 Joint_Report 数据（异步函数外部无法直接拿到 fetchAllData 的返回值，这里用 window.allData 作为全局变量存储）
                        const jointReport = window.allData && window.allData['Joint_Report'] ? window.allData['Joint_Report'] : [];

                        // 1. 找到根节点（主题）
                        let rootNodes = [];
                        if (response.data && Array.isArray(response.data)) {
                            rootNodes = response.data.filter(item => {
                                return item && item.theme_name === selectedTheme && 
                                       (item.status === '已完成' || item.status === '进行中');
                            });
                        }
                        console.log(`🔍 找到 ${rootNodes.length} 个根节点:`, rootNodes);

                        // 2. 绘制根节点及其子节点
                        rootNodes.forEach((item, index) => {
                            const nodeId = item.ID || `node_${index}`;
                            const nodeLabel = item.objective_name || `节点 ${index + 1}`;
                            nodes.add({
                                id: nodeId,
                                label: nodeLabel,
                                color: '#6aa84f',
                                title: `ID: ${nodeId}\n主题: ${item.theme_name || '未知'}\n状态: ${item.status || '未知'}`
                            });
                            // 查找 Joint_Report 中 Father_Node_ID 等于根节点 ID 的子节点
                            const children = jointReport.filter(child => child.Father_Node_ID == nodeId);
                            children.forEach((child, cidx) => {
                                const childId = child.ID || `child_${nodeId}_${cidx}`;
                                const childLabel = child.name || child.title || child.theme_name || `子节点 ${cidx + 1}`;
                                nodes.add({
                                    id: childId,
                                    label: childLabel,
                                    color: '#3b82f6',
                                    title: `ID: ${childId}\n类型: ${child.Node_Type || ''}\n状态: ${child.status || ''}`
                                });
                                edges.add({from: nodeId, to: childId, arrows: 'to'});
                                console.log(`📊 创建子节点 - ID: ${childId}, 父: ${nodeId}, 标签: ${childLabel}`);
                            });
                        });

                        // 如果没有根节点，创建一个默认节点
                        if (rootNodes.length === 0) {
                            nodes.add({
                                id: selectedTheme,
                                label: selectedTheme,
                                color: '#6aa84f',
                                title: '默认主题节点'
                            });
                            console.log(`📊 创建默认节点: ${selectedTheme}`);
                        }

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
                                console.log(`🎯 右键点击节点 ID: ${nodeId}`);
                                
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

                                // 初始设置 href，传入当前节点的 ID
                                updateNextButtonHref(nodeId);

                                // 添加事件监听器，传入当前节点的 ID
                                nodeTypeSelect.addEventListener('change', () => updateNextButtonHref(nodeId));

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