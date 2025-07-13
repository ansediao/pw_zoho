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
                    'Plans_Report',
                    'Plan_Nodes_Report'

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

                    // 解析自定义日期格式 (13-Jul-2025 01:55:33)
                    function parseCustomDate(dateStr) {
                        if (!dateStr) return new Date('1970-01-01');
                        
                        try {
                            // 月份映射
                            const monthMap = {
                                'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04',
                                'May': '05', 'Jun': '06', 'Jul': '07', 'Aug': '08',
                                'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
                            };
                            
                            // 解析格式: 13-Jul-2025 01:55:33
                            const parts = dateStr.split(' ');
                            if (parts.length !== 2) return new Date(dateStr);
                            
                            const datePart = parts[0]; // 13-Jul-2025
                            const timePart = parts[1]; // 01:55:33
                            
                            const dateComponents = datePart.split('-');
                            if (dateComponents.length !== 3) return new Date(dateStr);
                            
                            const day = dateComponents[0].padStart(2, '0');
                            const month = monthMap[dateComponents[1]] || '01';
                            const year = dateComponents[2];
                            
                            // 构建标准格式: YYYY-MM-DD HH:mm:ss
                            const standardFormat = `${year}-${month}-${day} ${timePart}`;
                            return new Date(standardFormat);
                        } catch (error) {
                            console.warn('日期解析失败:', dateStr, error);
                            return new Date('1970-01-01');
                        }
                    }

                    // 文字截断函数
                    function truncateText(text, maxLength) {
                        if (!text) return '';
                        if (text.length <= maxLength) return text;
                        return text.substring(0, maxLength) + '...';
                    }

                    // 获取节点颜色 - 根据层级和节点类型
                    function getNodeColor(level, nodeType) {
                        // 根节点颜色保持不变
                        if (level === 0) {
                            return { border: '#e74c3c', background: '#fadbd8' }; // 红色 - 根节点
                        }
                        
                        // 根据 Node_Type 设置颜色
                        const typeColors = {
                            'Goals': { border: '#3498db', background: '#d6eaf8' },     // 蓝色 - 目标
                            'Plans': { border: '#2ecc71', background: '#d5f4e6' },     // 绿色 - 计划
                            'Plan_Nodes': { border: '#f39c12', background: '#fdeaa7' }, // 橙色 - 计划节点
                            'Tasks': { border: '#9b59b6', background: '#e8daef' },     // 紫色 - 任务
                            'Issues': { border: '#e67e22', background: '#fadbd8' },    // 橙红色 - 问题
                            'default': { border: '#95a5a6', background: '#ecf0f1' }   // 灰色 - 默认
                        };
                        
                        return typeColors[nodeType] || typeColors['default'];
                    }

                    // 初始化网络图函数 - 使用行列布局
                    function initNetworkGraph(selectedTheme) {
                        const container = document.getElementById('networkGraphContainer');
                        const nodes = new vis.DataSet([]);
                        const edges = new vis.DataSet([]);

                        // 获取 Joint_Report 数据
                        const jointReport = window.allData && window.allData['Joint_Report'] ? window.allData['Joint_Report'] : [];
                        
                        // 数据验证函数 - 检查多父节点问题
                        function validateJointReportData(data) {
                            console.log("=== Joint Report 数据验证 ===");
                            const nodeParentCount = new Map();
                            const multiParentNodes = [];
                            
                            data.forEach(item => {
                                const nodeId = item.ID;
                                const parentId = item.Father_Node_ID;
                                
                                if (parentId && parentId !== "") {
                                    if (nodeParentCount.has(nodeId)) {
                                        const existingParents = nodeParentCount.get(nodeId);
                                        existingParents.push(parentId);
                                        if (existingParents.length === 2) {
                                            multiParentNodes.push({
                                                nodeId: nodeId,
                                                parents: existingParents,
                                                nodeName: item.objective_name || item.plan_name || item.plan_node_name || '未命名'
                                            });
                                        }
                                    } else {
                                        nodeParentCount.set(nodeId, [parentId]);
                                    }
                                }
                            });
                            
                            if (multiParentNodes.length > 0) {
                                console.warn(`⚠️ 发现 ${multiParentNodes.length} 个节点有多个父节点:`);
                                multiParentNodes.forEach(node => {
                                    console.warn(`  - 节点 ${node.nodeId} (${node.nodeName}) 的父节点: ${node.parents.join(', ')}`);
                                });
                            } else {
                                console.log("✅ 所有节点都只有一个父节点");
                            }
                            
                            return multiParentNodes;
                        }
                        
                        // 验证数据
                        const multiParentNodes = validateJointReportData(jointReport);

                        // 1. 找到根节点（主题）
                        let rootNodes = [];
                        if (response.data && Array.isArray(response.data)) {
                            rootNodes = response.data.filter(item => {
                                return item && item.theme_name === selectedTheme && 
                                       (item.status === '已完成' || item.status === '进行中');
                            });
                        }
                        console.log(`🔍 找到 ${rootNodes.length} 个根节点:`, rootNodes);

                        // 2. 构建层级数据结构
                        const allNodes = [];
                        const nodeMap = new Map();
                        const processedNodeIds = new Set(); // 记录已处理的节点ID，避免重复处理

                        // 收集所有节点数据
                        function collectNodes(item, level = 0, parentId = null) {
                            const nodeId = item.ID || `node_${Math.random().toString(36).slice(2)}`;
                            
                            // 如果节点已经被处理过，跳过（避免一个节点有多个父节点）
                            if (processedNodeIds.has(nodeId)) {
                                console.warn(`⚠️ 节点 ${nodeId} 已存在，跳过重复处理（避免多父节点）`);
                                return;
                            }
                            
                            const nodeData = {
                                id: nodeId,
                                name: item.objective_name || item.plan_name || item.plan_node_name || item.title || item.theme_name || `节点`,
                                father_id: parentId,
                                create_time: item.Create_Time || item.created_time || item.date_created || '01-Jan-1970 00:00:00',
                                level: level,
                                original: item
                            };
                            
                            allNodes.push(nodeData);
                            nodeMap.set(nodeId, nodeData);
                            processedNodeIds.add(nodeId); // 标记为已处理
                            
                            console.log(`✅ 处理节点: ${nodeId} (父节点: ${parentId || '无'}, 层级: ${level})`);

                            // 查找子节点
                            const children = jointReport.filter(child => child.Father_Node_ID == nodeId);
                            children.forEach(child => {
                                collectNodes(child, level + 1, nodeId);
                            });
                        }

                        // 从根节点开始收集
                        rootNodes.forEach(item => collectNodes(item, 0, null));

                        // 如果没有根节点，创建默认节点
                        if (rootNodes.length === 0) {
                            allNodes.push({
                                id: selectedTheme,
                                name: selectedTheme,
                                father_id: null,
                                create_time: '01-Jan-1970 00:00:00',
                                level: 0,
                                original: { theme_name: selectedTheme }
                            });
                        }

                        // 3. 按层级组织节点并排序
                        const levels = new Map();
                        const maxLevels = 3;

                        allNodes.forEach(node => {
                            if (node.level < maxLevels) {
                                if (!levels.has(node.level)) levels.set(node.level, []);
                                levels.get(node.level).push(node);
                            }
                        });

                        // 对每层的节点按父级排名优先排序
                        levels.forEach((levelNodes, level) => {
                            if (level === 0) {
                                // 根节点按创建时间排序
                                levelNodes.sort((a, b) => {
                                    const timeA = parseCustomDate(a.create_time);
                                    const timeB = parseCustomDate(b.create_time);
                                    return timeA - timeB; // 早的在前面
                                });
                            } else {
                                // 子节点按父级排名优先排序
                                levelNodes.sort((a, b) => {
                                    // 获取父节点在上一层的排序位置
                                    const parentLevelNodes = levels.get(level - 1) || [];
                                    const parentIndexA = parentLevelNodes.findIndex(p => p.id === a.father_id);
                                    const parentIndexB = parentLevelNodes.findIndex(p => p.id === b.father_id);
                                    
                                    // 如果父节点不同，按父节点的排序位置排序
                                    if (parentIndexA !== parentIndexB) {
                                        return parentIndexA - parentIndexB;
                                    }
                                    
                                    // 如果是同一个父节点的子节点，按创建时间排序
                                    const timeA = parseCustomDate(a.create_time);
                                    const timeB = parseCustomDate(b.create_time);
                                    return timeA - timeB;
                                });
                            }
                        });

                        // 4. 创建vis节点 - 行列布局
                        const visNodes = [];
                        const columnWidth = 300; // 列间距
                        const rowHeight = 100;   // 行间距
                        
                        // 创建节点位置映射，用于确保子节点不高于父节点
                        const nodePositions = new Map();
                        
                        levels.forEach((levelNodes, level) => {
                            levelNodes.forEach((node, index) => {
                                let yPosition;
                                
                                if (level === 0) {
                                    // 根节点固定在第一行 (y = 0)
                                    yPosition = 0;
                                } else {
                                    // 找到父节点的位置
                                    const parentPosition = nodePositions.get(node.father_id);
                                    if (parentPosition !== undefined) {
                                        if (index === 0) {
                                            // 第一个子节点与父节点同高
                                            yPosition = parentPosition;
                                        } else {
                                            // 后续子节点依次向下排列
                                            yPosition = parentPosition + index * rowHeight;
                                        }
                                    } else {
                                        // 如果找不到父节点，使用默认位置
                                        yPosition = index * rowHeight;
                                    }
                                }
                                
                                // 记录当前节点的位置
                                nodePositions.set(node.id, yPosition);
                                
                                visNodes.push({
                                    id: node.id,
                                    label: truncateText(node.name, 16),
                                    title: `${node.name}\n创建时间: ${node.create_time}\n类型: ${node.original.Node_Type || ''}\n状态: ${node.original.status || ''}`,
                                    level: level,
                                    x: level * columnWidth, // X坐标按层级 (列)
                                    y: yPosition, // Y坐标：第一个子节点与父节点同高，其他依次向下
                                    fixed: { x: true, y: true }, // 固定位置
                                    color: getNodeColor(level, node.original.Node_Type),
                                    font: { size: 14, color: '#333' },
                                    borderWidth: 2,
                                    margin: 10,
                                    widthConstraint: { minimum: 150, maximum: 200 },
                                    heightConstraint: { minimum: 50 },
                                });
                            });
                        });

                        // 5. 创建vis边 - 修复多父节点问题
                        const visEdges = [];
                        const nodeParentMap = new Map(); // 记录每个节点的唯一父节点
                        
                        // 第一步：为每个节点确定唯一的父节点
                        levels.forEach(levelNodes => {
                            levelNodes.forEach(node => {
                                if (node.father_id !== null && nodeMap.has(node.father_id)) {
                                    // 如果节点还没有父节点，直接设置
                                    if (!nodeParentMap.has(node.id)) {
                                        nodeParentMap.set(node.id, node.father_id);
                                        console.log(`📌 为节点 ${node.id} 设置父节点: ${node.father_id}`);
                                    } else {
                                        // 如果节点已有父节点，选择层级更小的（更接近根节点）
                                        const existingParent = nodeParentMap.get(node.id);
                                        const existingParentNode = nodeMap.get(existingParent);
                                        const currentParentNode = nodeMap.get(node.father_id);
                                        
                                        if (currentParentNode && existingParentNode && 
                                            currentParentNode.level < existingParentNode.level) {
                                            nodeParentMap.set(node.id, node.father_id);
                                            console.log(`🔄 为节点 ${node.id} 更新父节点: ${existingParent} → ${node.father_id} (选择更高层级)`);
                                        } else {
                                            console.warn(`⚠️ 节点 ${node.id} 已有父节点 ${existingParent}，忽略额外的父节点 ${node.father_id}`);
                                        }
                                    }
                                }
                            });
                        });
                        
                        // 第二步：根据确定的唯一父子关系创建边
                        nodeParentMap.forEach((parentId, nodeId) => {
                            const parentInView = visNodes.some(vn => vn.id === parentId);
                            const nodeInView = visNodes.some(vn => vn.id === nodeId);
                            
                            if (parentInView && nodeInView) {
                                visEdges.push({
                                    from: parentId,
                                    to: nodeId,
                                    arrows: { to: { enabled: true, scaleFactor: 1 } },
                                    color: '#7f8c8d',
                                    width: 2,
                                });
                                console.log(`✅ 创建边: ${parentId} → ${nodeId}`);
                            }
                        });

                        // 6. 添加到网络
                        nodes.add(visNodes);
                        edges.add(visEdges);

                        console.log(`📊 网络图数据: ${visNodes.length} 个节点, ${visEdges.length} 条边 (按创建时间排序)`)

                        const data = {
                            nodes: nodes,
                            edges: edges
                        };

                        // 7. 网络配置 - 禁用物理引擎，使用固定布局
                        const options = {
                            layout: {
                                hierarchical: {
                                    enabled: false,
                                },
                            },
                            physics: { enabled: false }, // 禁用物理引擎
                            nodes: {
                                shape: 'box',
                                margin: 10,
                                font: { size: 14, color: '#333' },
                                borderWidth: 2,
                                widthConstraint: { minimum: 150 },
                                heightConstraint: { minimum: 40 },
                                shadow: true
                            },
                            edges: {
                                arrows: { to: { enabled: true, scaleFactor: 1 } },
                                color: '#7f8c8d',
                                width: 2,
                                shadow: true
                            },
                            interaction: {
                                dragNodes: true,
                                dragView: true,
                                zoomView: true,
                                navigationButtons: true,
                                keyboard: true
                            }
                        };

                        const network = new vis.Network(container, data, options);

                        // 适应视图
                        setTimeout(() => {
                            if (network) {
                                network.fit();
                            }
                        }, 200);

                        network.on("oncontext", function(params) {
                            params.event.preventDefault(); // 阻止默认的浏览器右键菜单
                            const nodeId = network.getNodeAt(params.pointer.DOM);
                            if (nodeId) {
                                console.log(`🎯 右键点击节点 ID: ${nodeId}`);
                                
                                // 查找当前节点的数据，获取 Node_Type
                                const currentNode = allNodes.find(node => node.id === nodeId);
                                const nodeType = currentNode ? currentNode.original.Node_Type : null;
                                console.log(`📋 节点类型: ${nodeType}`);
                                
                                // 如果是 Plan_Nodes 节点，不显示右键菜单
                                if (nodeType === 'Plan_Nodes') {
                                    console.log(`🚫 Plan_Nodes 节点不显示右键菜单`);
                                    return;
                                }
                                
                                // 根据节点类型构建选项
                                let selectOptions = `
                                    <option value="purpose">目的</option>                               
                                    <option value="plan">计划</option>
                                `;
                                
                                // 只有当节点类型为 Plans 时才显示"计划节点"选项
                                if (nodeType === 'Plans') {
                                    selectOptions = `<option value="plan_node">计划节点</option>`;
                                }
                                
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
                                            ${selectOptions}
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