#### 2023년도에 구현한 게시판 웹 애플리케이션을 새로운 개발 환경에 맞게 Laravel Framework 버전을 업데이트 하였습니다.
>
> 호스팅 사이트: https://laravel.wderfer6790.click    
> 
> AWS Route53 도메인 구입 및 AWS EC2 인스턴스에 세팅

## 주요 구현 및 검증 내용

- **회원가입 및 이메일 발송**: 회원가입 과정과 Gmail SMTP를 이용한 인증 메일 발송 동작 확인
- **프로필 이미지 업로드**: 업로드된 이미지를 저장하고 웹에서 접근할 수 있도록 스토리지 경로 설정
- **데이터베이스 연동**: MySQL 연결, Laravel 마이그레이션 및 모델 팩토리를 이용한 테스트 데이터 생성
- **게시판 애플리케이션**: Laravel 기반 게시판 프로젝트의 동작과 유지보수

## 기존 기술 스택
| 구분 | 기술              |
| --- |-----------------|
| Backend | PHP74, Laravel8 |
| Database | MySQL           |
| Web Server | Apache          |
| Development Environment | Windows         |
| Mail | Gmail SMTP      |

## 변경된 기술 스택
| 구분 | 기술                          |
| --- |-----------------------------|
| Backend | PHP84, Laravel12            |
| Web Server | Nginx, PHP-FPM              |
| Development Environment | WSL, Docker, Docker Compose |

## 원격 저장소
**Docker Setup Repository:** [github.com/wderfer6790/docker_setup](https://github.com/wderfer6790/docker_setup)    
**Repository:** [github.com/wderfer6790/board_by_laravel](https://github.com/wderfer6790/board_by_laravel)

## 구현 화면
1. 게시글 목록(로그인 전/후)    
   ![게시글 목록 로그인 전](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/list_no_login.png)           
   ![게시글 목록 로그인 후](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/list_login.png)

2. 회원가입    
   ![회원가입 입력란](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/sign_form.png)
3. 회원가입 이메일 인증    
   ![회원가입 인증 이메일](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/sign_verify_mail.png)           
   ![회원가입 인증 이메일 확인](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/sign_verify_mail_checked.png)
4. 프로필    
   ![프로필](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/profile_form.png)
5. 게시글 작성    
   ![게시글 작성1](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/article_editor.png)        
   ![게시글 작성2](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/article_editor2.png)
6. 게시글    
   ![게시글](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/article.png)        
   ![게시글 댓글](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/article_reply.png)
7. 패스워드 초기화    
   ![패스워드 초기화](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/password_reset.png)
8. 패스트워 초기화 이메일 인증    
   ![패스워드 초기화 이메일](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/password_reset_verify_mail.png)
   ![패스워드 초기화 이메일 확인 후 재설정](https://raw.githubusercontent.com/wderfer6790/board_by_laravel/refs/heads/master/storage/app/public/image/readme/password_reset_verify_mail_checked.png)

