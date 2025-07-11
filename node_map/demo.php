<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>组织架构图 - 精简版</title>
    <script src="https://cdn.jsdelivr.net/npm/vis-network@latest/standalone/umd/vis-network.min.js"></script>
    <script src="https://js.zohostatic.com/creator/widgets/version/2.0/widgetsdk-min.js"></script>
    <!-- <script src="script.js"></script> -->
    <link rel="stylesheet" href="src/style.css" />
  </head>

  <body>
    <script>
            // 全局变量
      let network = null;
      let nodes = null;
      let edges = null;
      let rawData = [];
      let selectedNodeId = null;
      let nextNodeId = 10000;

      // 状态更新函数
      function updateStatus(message) {
        const statusDiv = document.getElementById('status');
        if (statusDiv) {
          statusDiv.textContent = message;
        }
        console.log('[状态]', message);
      }

      // 文字截断函数
      function truncateText(text, maxLength) {
        if (!text) return '';

        const lines = text.split('\n');
        const processedLines = lines.map(line => {
          let displayWidth = 0;
          let truncateIndex = 0;

          for (let i = 0; i < line.length; i++) {
            const char = line[i];
            const charWidth = /[\u4e00-\u9fa5]/.test(char) ? 2 : 1;

            if (displayWidth + charWidth > maxLength) {
              break;
            }

            displayWidth += charWidth;
            truncateIndex = i + 1;
          }

          if (truncateIndex < line.length) {
            return line.substring(0, truncateIndex) + '...';
          }

          return line;
        });

        return processedLines.join('\n');
      }

      // 数据转换函数：将 ZOHO API 数据转换为所需格式
      function transformZOHOData(response) {
        if (response.code !== 3000) {
          throw new Error('从 ZOHO 获取数据失败');
        }
        return response.data.map(item => ({
          id: parseInt(item.Node_ID, 10),
          father_id: item.Father_Node_ID ? parseInt(item.Father_Node_ID, 10) : null,
          name: item.Node_Name,
          create_time: item.Create_Time || '1970-01-01 00:00:00',
          description: item.Node_Des,
        }));
      }

      // 初始化网络
      function initNetwork() {
        updateStatus('正在初始化网络...');

        try {
          if (typeof vis === 'undefined') {
            throw new Error('vis-network 库未加载');
          }

          const container = document.getElementById('network-container');
          if (!container) {
            throw new Error('找不到网络容器');
          }

          nodes = new vis.DataSet();
          edges = new vis.DataSet();

          const options = {
            layout: {
              hierarchical: {
                enabled: false,
              },
            },
            physics: { enabled: false },
            nodes: {
              shape: 'box',
              margin: 10,
              font: { size: 14, color: '#333' },
              borderWidth: 2,
              widthConstraint: { minimum: 150 },
              heightConstraint: { minimum: 40 },
              color: {
                border: '#3498db',
                background: '#ecf0f1',
                highlight: { border: '#e74c3c', background: '#fadbd8' },
              },
            },
            edges: {
              arrows: { to: { enabled: true, scaleFactor: 1 } },
              color: '#7f8c8d',
              width: 2,
            },
            interaction: {
              dragNodes: true,
              dragView: true,
              zoomView: true,
            },
          };

          network = new vis.Network(container, { nodes, edges }, options);

          // 绑定事件
          network.on('click', function (params) {
            if (params.nodes.length > 0) {
              selectedNodeId = params.nodes[0];
            } else {
              selectedNodeId = null;
            }
            hideContextMenu();
          });

          // 右键菜单事件
          network.on('oncontext', function (params) {
            params.event.preventDefault();
            const nodeId = network.getNodeAt(params.pointer.DOM);
            if (nodeId !== undefined) {
              selectedNodeId = nodeId;
              showContextMenu(params.event.clientX, params.event.clientY);
            } else {
              hideContextMenu();
            }
          });

          // 点击空白处隐藏右键菜单
          document.addEventListener('click', hideContextMenu);

          updateStatus('网络初始化成功');
        } catch (error) {
          updateStatus('网络初始化失败: ' + error.message);
          console.error('初始化错误:', error);
        }
      }

      // 处理数据并限制为3层显示
      function processData(data) {
        try {
          rawData = data;

          // 清空现有数据
          nodes.clear();
          edges.clear();

          // 构建节点映射
          const nodeMap = new Map();
          data.forEach(item => nodeMap.set(item.id, item));

          // 找到根节点
          const rootNodes = data.filter(
            item => item.father_id === null || item.father_id === undefined
          );

          if (rootNodes.length === 0) {
            throw new Error('未找到根节点');
          }

          // 构建3层层级结构
          const levels = new Map();
          const maxLevels = 3; // 限制显示3层

          function buildLevels(nodeId, level = 0) {
            if (level >= maxLevels) return; // 限制层数

            if (!levels.has(level)) levels.set(level, []);

            const node = nodeMap.get(nodeId);
            if (node) {
              levels.get(level).push(node);

              // 找子节点并排序
              const children = data.filter(item => item.father_id === nodeId);
              children.sort((a, b) => {
                const timeA = new Date(a.create_time || '1970-01-01');
                const timeB = new Date(b.create_time || '1970-01-01');
                return timeA - timeB;
              });

              children.forEach(child => {
                buildLevels(child.id, level + 1);
              });
            }
          }

          rootNodes.forEach(root => buildLevels(root.id, 0));

          // 创建vis节点
          const visNodes = [];
          levels.forEach((levelNodes, level) => {
            levelNodes.forEach((node, index) => {
              visNodes.push({
                id: node.id,
                label: truncateText(node.name, 16),
                title: node.name, // 悬浮提示
                level: level,
                x: level * 250, // X坐标按层级
                y: index * 120, // Y坐标按排序索引
                fixed: { x: true, y: true }, // 固定位置
                color: getNodeColor(level),
                font: { size: 14, color: '#333' },
                borderWidth: 2,
              });
            });
          });

          // 创建vis边
          const visEdges = [];
          levels.forEach(levelNodes => {
            levelNodes.forEach(node => {
              if (node.father_id !== null && nodeMap.has(node.father_id)) {
                // 只有当父节点也在当前显示的节点中时才创建边
                const parentInView = visNodes.some(vn => vn.id === node.father_id);
                if (parentInView) {
                  visEdges.push({
                    from: node.father_id,
                    to: node.id,
                  });
                }
              }
            });
          });

          // 添加到网络
          nodes.add(visNodes);
          edges.add(visEdges);

          updateStatus(
            `数据加载完成: ${visNodes.length} 个节点, ${visEdges.length} 条边`
          );

          // 适应视图
          setTimeout(() => {
            if (network) {
              network.fit();
            }
          }, 200);
        } catch (error) {
          updateStatus('数据处理失败: ' + error.message);
          console.error('数据处理错误:', error);
        }
      }

      // 获取节点颜色
      function getNodeColor(level) {
        const colors = [
          { border: '#e74c3c', background: '#fadbd8' }, // 红色 - 第1层
          { border: '#3498db', background: '#d6eaf8' }, // 蓝色 - 第2层
          { border: '#2ecc71', background: '#d5f4e6' }, // 绿色 - 第3层
        ];

        return colors[Math.min(level, colors.length - 1)];
      }

      // 显示右键菜单
      function showContextMenu(x, y) {
        updateContextMenuState();

        const contextMenu = document.getElementById('contextMenu');
        contextMenu.style.left = x + 'px';
        contextMenu.style.top = y + 'px';
        contextMenu.style.display = 'block';
      }

      // 隐藏右键菜单
      function hideContextMenu() {
        const contextMenu = document.getElementById('contextMenu');
        contextMenu.style.display = 'none';
      }

      // 更新右键菜单状态
      function updateContextMenuState() {
        if (selectedNodeId === null) return;

        // 检查选中节点是否有子节点
        const hasChildren = rawData.some(item => item.father_id === selectedNodeId);

        // 获取下钻菜单项
        const drillDownItem = document.getElementById('drillDownMenuItem');

        if (hasChildren) {
          // 有子节点，启用下钻功能
          drillDownItem.classList.remove('disabled');
          drillDownItem.style.color = '#333';
          drillDownItem.style.cursor = 'pointer';
          drillDownItem.title = '下钻到此节点，显示其子树';
          drillDownItem.onclick = function () {
            drillDown();
            hideContextMenu();
          };
        } else {
          // 没有子节点，禁用下钻功能
          drillDownItem.classList.add('disabled');
          drillDownItem.style.color = '#999';
          drillDownItem.style.cursor = 'not-allowed';
          drillDownItem.title = '该节点没有子节点，无法下钻';
          drillDownItem.onclick = function () {
            updateStatus('该节点没有子节点，无法下钻');
            hideContextMenu();
          };
        }
      }

      // 下钻功能
      function drillDown() {
        if (selectedNodeId === null) {
          updateStatus('请先选择一个节点');
          return;
        }

        // 检查是否有子节点
        const hasChildren = rawData.some(item => item.father_id === selectedNodeId);

        if (!hasChildren) {
          updateStatus('该节点没有子节点，无法下钻');
          return;
        }

        // 获取选中节点及其所有后代
        function getAllDescendants(nodeId, data) {
          const children = data.filter(item => item.father_id === nodeId);
          let descendants = [...children];

          children.forEach(child => {
            descendants = descendants.concat(getAllDescendants(child.id, data));
          });

          return descendants;
        }

        const selectedNode = rawData.find(item => item.id === selectedNodeId);
        const descendants = getAllDescendants(selectedNodeId, rawData);

        // 创建新的数据集，以选中节点为根
        const drillData = [
          { ...selectedNode, father_id: null }, // 选中节点变为根节点
          ...descendants,
        ];

        // 重新处理数据
        processData(drillData);

        updateStatus('已下钻到节点: ' + selectedNode.name);
      }

      // 显示添加节点模态框
      function showAddNodeModal() {
        if (selectedNodeId === null) {
          updateStatus('请先选择一个父节点');
          return;
        }

        document.getElementById('nodeName').value = '';
        document.getElementById('nodeDescription').value = '';
        document.getElementById('addNodeModal').style.display = 'block';
        document.getElementById('nodeName').focus();

        document.getElementById('addNodeForm').onsubmit = function (e) {
          e.preventDefault();
          addNewNode();
        };
      }

      // 关闭添加节点模态框
      function closeAddNodeModal() {
        document.getElementById('addNodeModal').style.display = 'none';
      }

      // 添加新节点
      function addNewNode() {
        const nodeName = document.getElementById('nodeName').value.trim();
        const nodeDescription = document
          .getElementById('nodeDescription')
          .value.trim();

        if (!nodeName) {
          alert('请输入节点名称');
          return;
        }

        if (selectedNodeId === null) {
          alert('未选择父节点');
          return;
        }

        const newNodeId = nextNodeId++;
        const currentDate = new Date();
        const createTime =
          currentDate.toISOString().slice(0, 10) +
          ' ' +
          currentDate.toTimeString().slice(0, 8);

        const newNode = {
          id: newNodeId,
          father_id: selectedNodeId,
          name: nodeName,
          create_time: createTime,
          description: nodeDescription || '无描述',
        };

        // 添加到原始数据
        rawData.push(newNode);

        // 重新处理数据
        processData(rawData);

        closeAddNodeModal();
        updateStatus('已添加新节点: ' + nodeName);
      }

      // 页面加载完成后初始化
      window.addEventListener('load', async function () {
        updateStatus('页面加载完成');

        try {
          // 从 ZOHO 获取数据
          const config = {
            app_name: '-demo',
            report_name: 'demo_Report',
          };
          const response = await ZOHO.CREATOR.DATA.getRecords(config);
          console.log(response);

          const transformedData = transformZOHOData(response);

          // 初始化网络
          initNetwork();

          // 处理数据
          processData(transformedData);
        } catch (error) {
          updateStatus('初始化失败: ' + error.message);
          console.error('初始化错误:', error);
        }

        // 点击模态框外部关闭模态框
        window.onclick = function (event) {
          const modal = document.getElementById('addNodeModal');
          if (event.target === modal) {
            closeAddNodeModal();
          }
        };

        // 键盘快捷键
        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            closeAddNodeModal();
            hideContextMenu();
          }

          if (selectedNodeId === null) return;

          if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            showAddNodeModal();
          }

          if (e.key === 'Enter') {
            e.preventDefault();
            const hasChildren = rawData.some(
              item => item.father_id === selectedNodeId
            );
            if (hasChildren) {
              drillDown();
            } else {
              updateStatus('该节点没有子节点，无法下钻');
            }
          }
        });
      });

      // 错误处理
      window.addEventListener('error', function (e) {
        updateStatus('JavaScript错误: ' + e.message);
        console.error('全局错误:', e);
      });

      // var config = {
      //   action: 'open',
      //   url: 'https://creatorapp.zoho.com/compwj/demo/#demo',
      //   window: 'same',
      // };
      // ZOHO.CREATOR.UTIL.navigateParentURL(config);
    </script>
    <div id="network-container"></div>
    <div class="status" id="status">加载中...</div>

    <!-- 右键菜单 -->
    <div class="context-menu" id="contextMenu">
      <div
        class="context-menu-item"
        onclick="showAddNodeModal(); hideContextMenu();"
        title="为选中节点添加子节点"
      >
        📝 添加子节点
      </div>
      <div
        class="context-menu-item"
        id="drillDownMenuItem"
        onclick="drillDown()"
        title="下钻到此节点"
      >
        🔍 下钻查看
      </div>
    </div>

    <!-- 添加节点模态框 -->
    <div id="addNodeModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <span class="modal-title">添加新节点</span>
          <span class="close" onclick="closeAddNodeModal()">&times;</span>
        </div>
        <form id="addNodeForm">
          <div class="form-group">
            <label for="nodeName">节点名称 *</label>
            <input
              type="text"
              id="nodeName"
              name="nodeName"
              required
              placeholder="请输入节点名称"
            />
          </div>
          <div class="form-group">
            <label for="nodeDescription">节点描述</label>
            <textarea
              id="nodeDescription"
              name="nodeDescription"
              placeholder="请输入节点描述（可选）"
            ></textarea>
          </div>
          <div class="modal-buttons">
            <button
              type="button"
              class="btn btn-secondary"
              onclick="closeAddNodeModal()"
            >
              取消
            </button>
            <button type="submit" class="btn btn-primary">添加节点</button>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
