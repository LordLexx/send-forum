<?php

namespace Database\Seeders;

use App\Models\JargonTerm;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JargonTermsSeeder extends Seeder
{
    /**
     * Seeds the SEND Jargon Buster dictionary.
     *
     * Uses updateOrCreate keyed on `term`, so it is safe to re-run:
     * existing entries get refreshed definitions, but any extra_content
     * an admin has added is deliberately left untouched.
     */
    public function run(): void
    {
        $author = User::first();

        if (! $author) {
            throw new \RuntimeException(
                'No users found — run AdminUserSeeder first so jargon terms can be attributed to an author.'
            );
        }

        foreach ($this->terms() as [$term, $fullForm, $definition]) {
            JargonTerm::updateOrCreate(
                ['term' => $term],
                [
                    'full_form'  => $fullForm,
                    'definition' => $definition,
                    'slug'       => Str::slug($term),
                    'status'     => 'published',
                    'user_id'    => $author->id,
                ]
            );
        }

        $this->command?->info('Seeded ' . count($this->terms()) . ' jargon terms.');
    }

    /**
     * [term, full_form (or null), definition] — kept in the order of the
     * source list so entries are easy to cross-check.
     */
    private function terms(): array
    {
        return [
            // A
            ['AAC', 'Augmentative and Alternative Communication', 'Ways of communicating that support or replace speech — for example picture boards, signing, or electronic speech devices — used by people who find talking difficult.'],
            ['Additional Needs', null, 'A broad term for children who need extra help with learning, communication or physical needs beyond what is typically expected for their age.'],
            ['ADHD', 'Attention Deficit Hyperactivity Disorder', 'A condition that affects attention and behaviour. People with ADHD can seem restless, may have trouble concentrating and may act on impulse.'],
            ['ASC', 'Adult Social Care and Support', 'Care and support arranged by the council for adults (18+) who need extra help because of a disability, illness or caring role. Confusingly, ASC can also stand for Autistic Spectrum Condition — check the context it is used in.'],
            ['Advocacy', null, 'Free, independent support to help you have your voice heard. An advocate can attend meetings with you, help you understand reports and letters, and speak up for your rights.'],
            ['Aids and Adaptations', null, 'Equipment or changes to the home — such as grab rails, ramps, stair lifts or hoists — that help a disabled person live more safely and independently. An occupational therapist usually assesses what is needed.'],
            ['Annual Review', null, 'A legally required yearly meeting to review a child\'s Education, Health and Care Plan (EHCP). Progress towards outcomes is discussed and the plan is updated so it still meets the child\'s needs.'],
            ['AP', 'Alternative Provision', 'Education arranged for children who cannot attend their usual school — for example because of exclusion, illness, anxiety or special educational needs. It can include specialist units, one-to-one tuition or online learning.'],
            ['APD', 'Auditory Processing Disorder', 'A condition where the ears hear normally but the brain struggles to make sense of sound, especially speech in noisy places like classrooms.'],
            ['ARFID', 'Avoidant/Restrictive Food Intake Disorder', 'An eating difficulty where a person limits the foods they eat because of sensory sensitivity, fear of choking or vomiting, or little interest in food. It is not about body image.'],
            ['ASD', 'Autistic Spectrum Disorder or Condition', 'A lifelong developmental difference that affects how a person communicates, interacts with others and experiences the world. Often also written ASC (Autistic Spectrum Condition) or simply autism.'],
            ['Assessments', null, 'The process where professionals gather information about a child\'s needs — in education, health or care — to work out what support they should get. The biggest is the EHC needs assessment, which decides whether a child needs an EHCP.'],
            // B
            ['Benefits', null, 'Regular payments from the government to help with the extra costs of disability or caring — for example Disability Living Allowance (DLA), Personal Independence Payment (PIP) and Carer\'s Allowance.'],
            ['BSL', 'British Sign Language', 'The main sign language used by deaf people in the UK. It is a complete language in its own right, with its own grammar, and is used by some non-verbal children as their main way of communicating.'],
            // C
            ['CAMHS', 'Child and Adolescent Mental Health Services', 'NHS services that assess and support children and young people with emotional, behavioural or mental health difficulties. You can be referred by your GP, school or another professional.'],
            ['Care and Support Plan', null, 'A written plan from adult social care setting out what help a person needs and how it will be provided. Parent carers can also have their own carer\'s assessment and support plan.'],
            ['Carer', null, 'Anyone who provides regular, unpaid care for a family member or friend who could not manage without that help — including parent carers of children with SEND. Carers have a legal right to their own assessment.'],
            ['Carer\'s Allowance', null, 'A weekly benefit for people who care for someone for at least 35 hours a week, where that person receives certain disability benefits. Full-time students and people earning above a set limit may not qualify.'],
            ['Caseworker', null, 'The named person at the local authority who handles your child\'s EHC needs assessment and plan. They are your main point of contact for questions about the process.'],
            ['Cerebral Palsy', null, 'A physical condition affecting movement, posture and sometimes speech, caused by a difference in brain development before, during or soon after birth. Its effects range from mild to profound.'],
            ['CETR', 'Care, Education and Treatment Review', 'A meeting about a child or young person with a learning disability and/or autism who is at risk of being admitted to a mental health hospital — or is already in one. Family, commissioners and independent clinical experts check that the right community support is in place.'],
            ['CHC', 'Continuing Healthcare', 'Short for NHS Continuing Healthcare — a package of care arranged and fully paid for by the NHS for people with complex ongoing health needs. See our NHS Continuing Healthcare entry.'],
            ['CIN', 'Child in Need', 'A child who needs extra support from children\'s social care to stay healthy and develop well. A Child in Need plan sets out what help the family will receive — this is different to child protection.'],
            ['Citizen', null, 'Social care language for making clear that people who use services are equal members of society with the same rights as everyone else — not passive "service users".'],
            ['Client Contribution', null, 'The amount a person may be asked to pay towards their care after the council carries out a financial assessment. Some people pay nothing; it depends on income and savings.'],
            ['Co-production', null, 'Services being designed and delivered together with the people who use them — including parent carers — as equal partners. "Done with, not done to." Parent Carer Forums take part in co-production with the council and health services.'],
            ['Commissioner', null, 'The people who plan and pay for services. The council commissions education and social care; health services are commissioned by Integrated Care Boards (ICBs). When a service is missing, the commissioner is usually who to raise it with.'],
            ['CMHT', 'Community Mental Health Team', 'A team of NHS mental health professionals — psychiatrists, nurses, psychologists and social workers — who support people with mental health difficulties in the community rather than in hospital.'],
            ['Community Paediatricians', null, 'Children\'s doctors who work in the community rather than in hospital. They often carry out developmental assessments for children with suspected autism, ADHD or other needs, and contribute to EHC needs assessments.'],
            ['Community Services', null, 'Health services delivered outside of hospital — in clinics, schools and people\'s homes. Includes health visitors, school nurses, therapists and district nurses. Sometimes called "out of hospital" services.'],
            ['Comorbidity', null, 'When one person has two or more conditions at the same time — for example autism and ADHD. Also called "co-occurring conditions".'],
            ['CP', 'Child Protection', 'The processes that begin when professionals believe a child may be at risk of harm, possibly leading to a child protection plan. CP can also stand for cerebral palsy — check the context.'],
            // D
            ['DCD', 'Developmental Co-ordination Disorder', 'A condition affecting physical co-ordination that makes movements like balance, handwriting and using cutlery harder than expected for a child\'s age. Often still called dyspraxia.'],
            ['DfE', 'Department for Education', 'The government department responsible for children\'s services and education in England. It publishes the SEND Code of Practice and funds parent carer forums.'],
            ['DFG', 'Disabled Facilities Grant', 'A council grant that pays for adaptations to a home so a disabled person can live there more easily — for example a level-access shower, stair lift or ramp. It is means-tested for adults, but not for families of disabled children.'],
            ['DP', 'Direct Payments', 'Money paid directly to you by the council so you can arrange and buy the care or support your family needs yourself, instead of having the council arrange services for you. Part of a personal budget.'],
            ['Disagreement Resolution', null, 'An independent, informal way to sort out a dispute with the council or health commissioners about SEND support — before or without going to tribunal. The council must tell you about these services.'],
            ['DLA', 'Disability Living Allowance', 'A tax-free benefit for children under 16 who need extra looking after or have difficulty getting about. Many families of children with SEND claim DLA, and it can unlock extra help for the whole household.'],
            ['DSA', 'Disabled Students\' Allowance', 'Financial help for students in higher education with a disability, long-term health condition, mental health condition or specific learning difficulty such as dyslexia. Pays for things like specialist equipment and study support.'],
            ['DSR', 'Dynamic Support Register', 'A list kept by the council and NHS of people with a learning disability and/or autism who may be at risk of crisis or hospital admission, so extra support can be planned early. Linked to Care, Education and Treatment Reviews (CETRs).'],
            ['Dyscalculia', null, 'A specific learning difficulty that affects understanding numbers and maths — counting, remembering number facts, and grasping quantities, time and money.'],
            ['Dyslexia', null, 'A specific learning difficulty mainly affecting accurate and fluent reading, writing and spelling. It has nothing to do with intelligence — dyslexic people often have strengths in creativity, problem-solving and big-picture thinking.'],
            // E
            ['EAL', 'English as an Additional Language', 'Describes children whose first language at home is not English. It is not a special educational need, but schools should take it into account when assessing a child.'],
            ['EHA', 'Early Help Assessment', 'A whole-family assessment used when there are emerging concerns — about a child\'s needs, parenting, or pressures like housing. It brings services together early, before problems escalate.'],
            ['EBD', 'Emotional and Behavioural Difficulties', 'An older term for children who find managing their emotions or behaviour at school very difficult. Often now described more specifically — for example as anxiety, ADHD or trauma-related needs.'],
            ['EBSA', 'Emotionally Based School Avoidance', 'When a child finds attending school very hard because of anxiety or distress — not because they don\'t want to learn. Language like "school refusal" is now avoided because it sounds like a choice.'],
            ['EBSNA', 'Emotionally Based School Non-Attendance', 'A newer term covering the same idea as EBSA: a child is not attending school because their emotional needs make it feel impossible. Schools should put support in place rather than treat it as truancy.'],
            ['EHCP', 'Education, Health and Care Plan', 'A legally binding document for children and young people up to 25 with the most complex special educational needs. It sets out the child\'s needs and the support they must receive — and the council must secure it.'],
            ['EOTAS', 'Education Other than At School', 'Arrangements where the council secures a child\'s education somewhere other than a school — for example a home programme, hospital school or specialist provider — and names it in the EHCP.'],
            ['EP', 'Educational Psychologist', 'A specialist in how children learn and develop. EPs assess children\'s learning, memory and emotional needs and advise schools and the council on the right support. Often called "Ed Psych".'],
            ['EYS', 'Early Years Settings', 'Childcare and education places for children under five — nurseries, pre-schools, childminders and reception. SEND support and early identification often start here.'],
            // F
            ['FE', 'Further Education', 'Education after school — typically at college from age 16. FE colleges offer many supported courses and pathways for young people with SEND, sometimes up to age 25 with an EHCP.'],
            // G
            ['GAD', 'Generalised Anxiety Disorder', 'A long-term condition where a person feels anxious about a wide range of situations and issues most days, rather than one specific thing — making it hard to relax, concentrate or sleep.'],
            ['GDD', 'Global Developmental Delay', 'A term used when a young child takes much longer than others their age to reach milestones in several areas, such as walking, talking or playing. It may become clearer over time whether a specific condition is involved.'],
            // H
            ['HE', 'Home Education', 'When parents take responsibility for educating their child at home rather than at school — sometimes called Elective Home Education (EHE). Some families choose it for a child with SEND; the council must be told and makes informal enquiries about the education provided.'],
            ['Healthwatch England', null, 'The national body that gathers the views of people who use health and social care and speaks up for them. Every area also has a local Healthwatch you can feed your experiences back to.'],
            ['HI', 'Hearing Impairment', 'Describes children with any level of hearing loss, from mild to profound. Support in school can include hearing aids, radio aids and specialist teaching from a Teacher of the Deaf.'],
            ['HLTA', 'Higher Level Teaching Assistant', 'A teaching assistant with extra training who can lead whole classes and support learning under a teacher\'s direction — sometimes part of a SEND child\'s support timetable.'],
            ['Home Care', null, 'Paid care and support delivered in a person\'s own home — help with things like washing, dressing, meals and medication — arranged through a social care assessment.'],
            // I
            ['IEP', 'Individual Education Plan', 'A school\'s own plan of the targets and extra support for a child with SEN — small-step goals, who helps with what, and how progress is checked. It is not a legal document like an EHCP, and schools use different names for it.'],
            ['IHP', 'Individual Health Plan', 'A written plan for a child\'s health needs at school — for example epilepsy, diabetes or asthma — agreed between school, parents and the school nurse. It says what care is given, when and by whom.'],
            ['Inclusion', null, 'The principle that children with SEND should be welcomed and supported to learn alongside their peers in ordinary schools wherever possible, taking part fully in school life.'],
            ['Independent living', null, 'Having real choice and control over your own life as an adult — where and how you live, and who supports you — rather than being placed in a service. Supported living and direct payments can help.'],
            ['Independent school', null, 'A fee-paying private school. Some specialise in SEND, and a small number are government-approved to be named in an EHCP — only those can be funded by the council.'],
            ['INT', 'Integrated Neighbourhood Teams', 'New local teams that join up GP practices, community nurses, social care and mental health staff so families deal with one connected team in their neighbourhood rather than lots of separate services.'],
            ['Integrated Care', null, 'Health and social care organisations working together as one joined-up system around a person — sharing information and planning together — instead of families having to repeat their story at every service.'],
            ['ICB', 'Integrated Care Board', 'The NHS organisation that plans and pays for health services in your area — it replaced Clinical Commissioning Groups (CCGs) in 2022. The ICB is responsible for the health sections of an EHCP.'],
            ['ICP', 'Integrated Care Partnership', 'A broad alliance that brings the NHS, councils, the voluntary sector and others together to agree a plan for improving health and care across a whole area.'],
            ['ICS', 'Integrated Care System', 'The largest partnership of all — NHS bodies, councils and other organisations covering a whole region, jointly planning health and care. ICBs and ICPs sit inside an ICS.'],
            ['IS', 'Independent Supporter', 'Someone trained and independent of the council who helps parents understand the EHC needs assessment process, prepare for meetings and express their views. Different from an advocate — see Advocacy.'],
            // K
            ['Key worker', null, 'One named professional who is your family\'s main point of contact and links up all the different services around a child with complex needs, so you are not passed from person to person.'],
            // L
            ['LA', 'Local Authority', 'Your local council. In SEND it carries out EHC needs assessments, maintains EHCPs, and has the legal duty to secure the special educational provision in a plan.'],
            ['LAEP', null, 'An abbreviation that varies between local areas — most often used for a Local Area Inclusion Plan: a shared plan between the council, NHS and schools setting out how SEND provision will be improved locally. Some areas use the letters differently, so check the context.'],
            ['Learning Difficulties', null, 'A general term for finding learning harder than most — for example a specific difficulty such as dyslexia, or learning new things more slowly. "Learning difficulty" is broader than "learning disability".'],
            ['Learning Disabilities', null, 'A lifelong reduced ability to understand new or complex information and learn skills, present from childhood, that affects everyday life. Used when the difficulty is across all areas of learning — as distinct from specific learning difficulties like dyslexia.'],
            ['Local Offer', null, 'Every council must publish a Local Offer — an online guide to all the SEND services, support, schools and activities available locally, plus how to ask for an assessment. Start here when looking for what exists.'],
            // M
            ['Mediation', null, 'A free, informal meeting run by an independent mediator to try to resolve a disagreement with the council about EHC assessments or plans. Parents must be offered mediation before most tribunal appeals; you can decline and receive a certificate.'],
            ['MHST', 'Mental Health Support Team', 'NHS teams that work in and with schools to give early help for mild to moderate mental health difficulties — things like anxiety and low mood — through one-to-one and group support.'],
            ['MLD', 'Moderate Learning Difficulties', 'A level of learning need where a child learns more slowly than their peers and needs extra support with most school work, but can still make progress alongside a modified curriculum.'],
            ['MSI', 'Multi-Sensory Impairment', 'When a child has difficulties with both hearing and vision — sometimes called deafblindness — which together have a much bigger impact on communication and learning than either alone.'],
            ['MASH', 'Multi-Agency Safeguarding Hub', 'The single team where police, social care, health and education staff sit together to look at concerns raised about a child and decide quickly what should happen next. If a professional "makes a MASH referral", this is where it goes.'],
            // N
            ['National Curriculum', null, 'What all state schools in England must teach, subject by subject, with targets for each age group. Children with SEND may work below age-related expectations — their progress is measured differently, and an EHCP can modify what is taught.'],
            ['NEET', 'Not in Education, Employment or Training', 'A label for 16–24 year-olds who are not in school, college, work or training. Good transition planning from Year 9 aims to prevent young people with SEND becoming NEET.'],
            ['Neurodivergent', null, 'Describes people whose brains process the world differently to what is considered "typical" — including autistic people and people with ADHD, dyslexia, dyspraxia and more. Many prefer it as a positive identity term rather than "disorder".'],
            ['Neurotypical', null, 'Describes someone whose thinking and processing style matches what society expects as "typical" — the opposite of neurodivergent.'],
            ['NHS Continuing Healthcare', null, 'A package of ongoing care arranged and fully funded by the NHS for people with complex health needs — free, unlike social care. It follows a detailed assessment; the children\'s version is called Continuing Care.'],
            ['NHS England', null, 'The national body that leads the NHS in England — commissioning specialist services, setting standards, and funding programmes such as the Dynamic Support Register work.'],
            ['Non-Maintained Special School', null, 'A special school run by a charity or trust rather than the council or a private owner, and government-approved to take children with EHCPs. The council funds the places it names there.'],
            // O
            ['Ofsted', 'Office for Standards in Education, Children\'s Services and Skills', 'The government inspector that visits and grades schools. Together with the CQC (health regulator), it also inspects how well a whole local area does its job for children with SEND.'],
            ['OT', 'Occupational Therapy', 'Therapy focused on everyday activities. For children, occupational therapists help with sensory processing, fine motor skills like handwriting, and independence skills such as dressing and eating.'],
            ['Outcomes', null, 'The goals written into an EHCP or care plan — what should be different for the child, and by when, such as "make friends at break times" or "travel independently". Progress towards outcomes is reviewed at annual review.'],
            // P
            ['Paediatrics', null, 'The branch of medicine that cares for babies, children and young people. A paediatrician is a children\'s doctor — community paediatricians often do developmental assessments for SEND.'],
            ['Parent Carer Forum', null, 'A group of parents and carers of children with SEND that works with the council, health and education services to design and improve them — representing the voice of local families. Slough SEND PCF is yours!'],
            ['PCN', 'Primary Care Network', 'Groups of GP surgeries working together with community and social care staff to serve a local population. Your PCN may run services like health checks and social prescribing.'],
            ['PDA', 'Pathological Demand Avoidance', 'A profile some autistic people have where everyday demands — even nice ones — trigger intense anxiety and a need to avoid them, driven by a need for control. Some now prefer "Persistent Drive for Autonomy" as a name.'],
            ['Personal Assistant', null, 'Someone you employ directly — usually using direct payments — to support your child or family, at home or out in the community. You become the employer, with the responsibilities that come with that.'],
            ['PB', 'Personal Budget', 'The money identified to pay for the support set out in an EHCP or a care plan — giving families choice and control over how it is spent. You can manage it yourself (direct payments), ask the council to hold it, or use a mix.'],
            ['PIP', 'Personal Independence Payment', 'A benefit for people aged 16 up to state pension age with a long-term physical or mental health condition or disability, helping with extra living costs. It has two parts — daily living and mobility.'],
            ['PMLD', 'Profound and Multiple Learning Disability', 'When someone has a severe learning disability and other significant disabilities — affecting movement, communication, eating or health — and needs high levels of support with most aspects of daily life.'],
            ['Portage', null, 'A home-visiting service for pre-school children with SEND and their families. A trained portage worker visits regularly and helps you teach your child new skills through small steps and play.'],
        ];
    }
}
