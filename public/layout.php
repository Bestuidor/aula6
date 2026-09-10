<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Site de Movimentação</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">


    <!-- NAVBAR -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">

        <div class="container-fluid">


            <!-- NOME DO SITE -->

            <a class="navbar-brand" href="/index.php">
                Sistema de Comtrole!
            </a>


            <!-- BOTÃO MOBILE -->

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarSistema"
            >

                <span class="navbar-toggler-icon"></span>

            </button>


            <!-- MENU -->

            <div
                class="collapse navbar-collapse"
                id="navbarSistema"
            >

                <ul class="navbar-nav me-auto">


                    <!-- ==================== -->
                    <!-- PESSOAS -->
                    <!-- ==================== -->

                    <li class="nav-item dropdown">

                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                        >
                            Pessoas
                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="/pessoa-create.php"
                                >
                                    Cadastrar
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="/listar.php"
                                >
                                    Listar
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="/pesquisar.php"
                                >
                                    Pesquisar
                                </a>
                            </li>

                        </ul>

                    </li>


                    <!-- ==================== -->
                    <!-- MOVIMENTAÇÃO -->
                    <!-- ==================== -->

                    <li class="nav-item dropdown">

                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                        >
                            Movimentação
                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="/movimentacao-create.php"
                                >
                                    Movimentar
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="/movimentacao-list.php"
                                >
                                    Lista de Movimentaçao
                                </a>
                            </li>

                        </ul>

                    </li>


                </ul>

            </div>

        </div>

    </nav>


    <!-- CONTEÚDO DA PÁGINA -->

    <div class="container mt-4">

        <?php
        echo isset($content) ? $content : "";
        ?>

    </div>


    <!-- BOOTSTRAP -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>