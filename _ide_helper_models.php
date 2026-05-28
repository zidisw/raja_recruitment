<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $job_id
 * @property int $status
 * @property \App\Enums\RecruitmentStage $recruitment_stage
 * @property string|null $hr_notes
 * @property string|null $interviewer_notes
 * @property \Carbon\CarbonImmutable|null $stage_updated_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $candidate
 * @property-read \App\Models\Interview|null $hrInterview
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Interview> $interviews
 * @property-read int|null $interviews_count
 * @property-read \App\Models\Job $job
 * @property-read \App\Models\Mcu|null $mcu
 * @property-read \App\Models\OfferingLetter|null $offeringLetter
 * @property-read \App\Models\Onboarding|null $onboarding
 * @property-read \App\Models\Psychotest|null $psychotest
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationStageLog> $stageLogs
 * @property-read int|null $stage_logs_count
 * @property-read \App\Models\Interview|null $userInterview
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereHrNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereInterviewerNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereRecruitmentStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereStageUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereUserId($value)
 */
	class Application extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $application_id
 * @property \App\Enums\RecruitmentStage $stage
 * @property string $decision
 * @property string $notes
 * @property int $decided_by
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \App\Models\Application $application
 * @property-read \App\Models\User $decidedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStageLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStageLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStageLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStageLog whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStageLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStageLog whereDecidedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStageLog whereDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStageLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStageLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStageLog whereStage($value)
 */
	class ApplicationStageLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string|null $image_path
 * @property string|null $category
 * @property int $author_id
 * @property bool $is_published
 * @property \Carbon\CarbonImmutable|null $published_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $author
 * @property-read \App\Models\ArticleImage|null $featuredImage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArticleImage> $images
 * @property-read int|null $images_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereUpdatedAt($value)
 */
	class Article extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $article_id
 * @property string $path
 * @property bool $is_featured
 * @property int $sort_order
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Article $article
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleImage whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleImage whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleImage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleImage whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleImage whereUpdatedAt($value)
 */
	class ArticleImage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $degree
 * @property string $institution_name
 * @property string $major
 * @property string $start_year
 * @property string|null $end_year
 * @property string|null $gpa
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation whereEndYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation whereGpa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation whereInstitutionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation whereMajor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation whereStartYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateEducation whereUserId($value)
 */
	class CandidateEducation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $company_name
 * @property string $position
 * @property \Carbon\CarbonImmutable $start_date
 * @property \Carbon\CarbonImmutable|null $end_date
 * @property bool $is_current
 * @property numeric|null $last_salary
 * @property string|null $job_description
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience whereIsCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience whereJobDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience whereLastSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateExperience whereUserId($value)
 */
	class CandidateExperience extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $organization_name
 * @property string $position
 * @property \Carbon\CarbonImmutable $start_date
 * @property \Carbon\CarbonImmutable|null $end_date
 * @property bool $is_current
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateOrganization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateOrganization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateOrganization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateOrganization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateOrganization whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateOrganization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateOrganization whereIsCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateOrganization whereOrganizationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateOrganization wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateOrganization whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateOrganization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateOrganization whereUserId($value)
 */
	class CandidateOrganization extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $nik
 * @property string $place_of_birth
 * @property \Carbon\CarbonImmutable $date_of_birth
 * @property string $gender
 * @property string $religion
 * @property string $marital_status
 * @property string $address_ktp
 * @property string $address_domicile
 * @property string $whatsapp
 * @property string|null $linkedin_url
 * @property string|null $ktp_path
 * @property string|null $photo_path
 * @property string|null $portfolio_path
 * @property string|null $certificate_path
 * @property string|null $paklaring_path
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereAddressDomicile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereAddressKtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereCertificatePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereKtpPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereLinkedinUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereMaritalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile wherePaklaringPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile wherePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile wherePlaceOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile wherePortfolioPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidateProfile whereWhatsapp($value)
 */
	class CandidateProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Job> $jobs
 * @property-read int|null $jobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\DepartmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 */
	class Department extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \App\Enums\RecruitmentStage $stage
 * @property string $job_level
 * @property string $subject
 * @property string $body
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereJobLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereUpdatedAt($value)
 */
	class EmailTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $application_id
 * @property string $interview_type
 * @property int $interviewer_id
 * @property \Carbon\CarbonImmutable $scheduled_at
 * @property string|null $meeting_link
 * @property string|null $evaluation_path
 * @property string|null $hr_notes
 * @property string $status
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Application $application
 * @property-read \App\Models\User $interviewer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview whereEvaluationPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview whereHrNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview whereInterviewType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview whereInterviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview whereMeetingLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Interview whereUpdatedAt($value)
 */
	class Interview extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $requirements
 * @property string|null $benefits
 * @property \App\Enums\JobLevel $level
 * @property bool $is_active
 * @property \Carbon\CarbonImmutable|null $closed_at
 * @property int $created_by
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property int|null $department_id
 * @property int|null $site_id
 * @property int|null $ptk_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Application> $applications
 * @property-read int|null $applications_count
 * @property-read \App\Models\User $creator
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\JobImage|null $featuredImage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JobImage> $images
 * @property-read int|null $images_count
 * @property-read \App\Models\Ptk|null $ptk
 * @property-read \App\Models\Site|null $site
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereBenefits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job wherePtkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereRequirements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereUpdatedAt($value)
 */
	class Job extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $job_id
 * @property string $path
 * @property bool $is_featured
 * @property int $sort_order
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Job $job
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobImage whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobImage whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobImage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobImage whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobImage whereUpdatedAt($value)
 */
	class JobImage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $application_id
 * @property \Carbon\CarbonImmutable $mcu_date
 * @property string $result
 * @property string|null $notes
 * @property string|null $file_path
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Application $application
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mcu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mcu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mcu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mcu whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mcu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mcu whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mcu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mcu whereMcuDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mcu whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mcu whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mcu whereUpdatedAt($value)
 */
	class Mcu extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $application_id
 * @property \Carbon\CarbonImmutable $offer_date
 * @property string|null $file_path
 * @property string|null $signed_file_path
 * @property \Carbon\CarbonImmutable|null $signed_at
 * @property string $status
 * @property string|null $candidate_notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Application $application
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter whereCandidateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter whereOfferDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter whereSignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter whereSignedFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfferingLetter whereUpdatedAt($value)
 */
	class OfferingLetter extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $application_id
 * @property \Carbon\CarbonImmutable $joining_date
 * @property \Carbon\CarbonImmutable|null $onsite_date
 * @property string|null $onsite_location
 * @property string|null $onsite_notes
 * @property string $onboarding_status
 * @property string|null $travel_ticket_number
 * @property string|null $travel_ticket_notes
 * @property \Carbon\CarbonImmutable|null $travel_ticket_sent_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Application $application
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding whereJoiningDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding whereOnboardingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding whereOnsiteDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding whereOnsiteLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding whereOnsiteNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding whereTravelTicketNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding whereTravelTicketNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding whereTravelTicketSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Onboarding whereUpdatedAt($value)
 */
	class Onboarding extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $application_id
 * @property \Carbon\CarbonImmutable $test_date
 * @property string $result
 * @property string|null $notes
 * @property string|null $file_path
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Application $application
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Psychotest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Psychotest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Psychotest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Psychotest whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Psychotest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Psychotest whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Psychotest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Psychotest whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Psychotest whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Psychotest whereTestDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Psychotest whereUpdatedAt($value)
 */
	class Psychotest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nomor_ptk
 * @property string|null $department
 * @property string $posisi
 * @property int $jumlah_kebutuhan
 * @property string|null $alasan_permintaan
 * @property \Carbon\CarbonImmutable|null $tanggal_permintaan
 * @property string $status
 * @property int|null $created_by
 * @property string|null $attachment_path
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Job> $jobs
 * @property-read int|null $jobs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk whereAlasanPermintaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk whereAttachmentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk whereJumlahKebutuhan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk whereNomorPtk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk wherePosisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk whereTanggalPermintaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ptk whereUpdatedAt($value)
 */
	class Ptk extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $location
 * @property string|null $description
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Job> $jobs
 * @property-read int|null $jobs_count
 * @method static \Database\Factories\SiteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Site whereUpdatedAt($value)
 */
	class Site extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $host
 * @property int $port
 * @property string $encryption
 * @property string $username
 * @property string $password
 * @property string $from_address
 * @property string $from_name
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting whereEncryption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting whereFromAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting whereFromName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmtpSetting whereUsername($value)
 */
	class SmtpSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property \App\Enums\UserRole $role
 * @property int|null $department_id
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Application> $applications
 * @property-read int|null $applications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Job> $createdJobs
 * @property-read int|null $created_jobs_count
 * @property-read \App\Models\Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CandidateEducation> $education
 * @property-read int|null $education_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CandidateExperience> $experiences
 * @property-read int|null $experiences_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CandidateOrganization> $organizations
 * @property-read int|null $organizations_count
 * @property-read \App\Models\CandidateProfile|null $profile
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

